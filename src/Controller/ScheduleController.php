<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\ScheduleItem;
use App\Entity\Subject;
use App\Entity\User;
use App\Requests\ScheduleController\AddScheduleItemRequest;
use App\Requests\ScheduleController\CreateScheduleRequest;
use App\Requests\ScheduleController\PatchScheduleItemRequest;
use App\Requests\ScheduleController\UpdateScheduleItemRequest;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class ScheduleController extends AbstractController
{
    private User $user;

    public function __construct(private readonly EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[Route('/user_schedules', methods: ['GET'])]
    public function getUserSchedules(): JsonResponse
    {
        if (in_array('ROLE_ADMIN', $this->user->getRoles())) {
            $userSchedules = $this->entityManager->getRepository(Schedule::class)->findAll();
        }
        else
            $userSchedules = $this->user->getSchedules();

        return $this->json($userSchedules, Response::HTTP_OK, [], ['groups' => ['user_schedule']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/link/{scheduleId<\d+>}/{userId<\d+>}', methods: ['POST'])]
    public function linkScheduleWithUser(int $scheduleId, int $userId): JsonResponse
    {
        $schedule = $this->entityManager->getRepository(Schedule::class)->find($scheduleId);
        if (!$schedule)
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user)
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);

        $user->addSchedule($schedule);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['success' => 'Schedule linked successfully'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/batch_link/{scheduleId<\d+>}', methods: ['POST'])]
    public function BatchLinkScheduleWithUsers(int $scheduleId, Request $request): JsonResponse
    {
        $schedule = $this->entityManager->getRepository(Schedule::class)->find($scheduleId);
        if (!$schedule)
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);

        $usersIds = json_decode($request->getContent(), true)['usersIds'] ?? [];

        $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $usersIds]);
        foreach ($users as $user) {
            $user->addSchedule($schedule);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        return $this->json(['success' => 'Schedule linked successfully'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/batch_unlink/{scheduleId<\d+>}', methods: ['POST'])]
    public function BatchUnlinkScheduleWithUsers(int $scheduleId, Request $request): JsonResponse
    {
        $schedule = $this->entityManager->getRepository(Schedule::class)->find($scheduleId);
        if (!$schedule)
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);

        $usersIds = json_decode($request->getContent(), true)['usersIds'] ?? [];

        $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $usersIds]);
        foreach ($users as $user) {
            $user->removeSchedule($schedule);
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();

        return $this->json(['success' => 'Schedule linked successfully'], Response::HTTP_OK);
    }

    #[Route('/schedule/{id<\d+>}', methods: ['GET'])]
    public function getSchedule(int $id): JsonResponse
    {
        $schedule = $this->entityManager->getRepository(Schedule::class)->findScheduleWithSortedItems($id);

        if (!$schedule)
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);

        if (!in_array('ROLE_ADMIN', $this->user->getRoles())) {
            $userSchedules = $this->user->getSchedules();
            if (!$userSchedules->contains($schedule))
                return $this->json(['error' => 'Schedule not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($schedule, Response::HTTP_OK, [], ['groups' => ['schedule_with_items']]);
    }

    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule', methods: ['POST'])]
    public function addScheduleItem(AddScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $schedule = $this->entityManager->getRepository(Schedule::class)->find($data['scheduleId']);
        if (!$schedule)
            return $this->json(['error' => 'Schedule not found'], Response::HTTP_BAD_REQUEST);

        $subject = $this->entityManager->getRepository(Subject::class)->find($data['subjectId']);
        if (!$subject)
            return $this->json(['error' => 'Subject not found'], Response::HTTP_BAD_REQUEST);

        // Проверка на пересечение расписания
        $existingScheduleItems = $this->entityManager
            ->getRepository(ScheduleItem::class)
            ->findOverlappingItems($schedule->getId(), $data['dayOfWeek'], new DateTime($data['startTime']), new DateTime($data['endTime']));

        if (!empty($existingScheduleItems))
            return $this->json(['error' => 'Time slot is already taken'], Response::HTTP_CONFLICT);


        $startTimeDT = new DateTime($data['startTime']);
        $endTimeDT = new DateTime($data['endTime']);
        if ($startTimeDT > $endTimeDT)
            return $this->json(['error' => 'startTime cannot be larger than endTime'], Response::HTTP_BAD_REQUEST);

        $scheduleItem = new ScheduleItem();
        $scheduleItem->setDayOfWeek($data['dayOfWeek']);
        $scheduleItem->setSubject($subject);
        $scheduleItem->setStartTime($startTimeDT);
        $scheduleItem->setEndTime($endTimeDT);
        $scheduleItem->setCreatedAt(new DateTimeImmutable());
        $scheduleItem->setSchedule($schedule);

        $this->entityManager->persist($scheduleItem);
        $this->entityManager->flush();

        return $this->json($scheduleItem, Response::HTTP_CREATED, [], ['groups' => ['schedule_item']]);
    }

    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/{id<\d+>}', methods: ['PUT'])]
    public function updateScheduleItem(int $id, UpdateScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $startTimeDT = new DateTime($data['startTime']);
        $endTimeDT = new DateTime($data['endTime']);
        if ($startTimeDT > $endTimeDT)
            return $this->json(['error' => 'startTime cannot be larger than endTime'], Response::HTTP_BAD_REQUEST);

        $scheduleItemForUpdate = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForUpdate)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

        $subject = $this->entityManager->getRepository(Subject::class)->find($data['subjectId']);
        if (!$subject)
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);

        // Проверка на пересечение расписания
        $existingScheduleItems = $this->entityManager
            ->getRepository(ScheduleItem::class)
            ->findOverlappingItems($scheduleItemForUpdate->getSchedule()->getId(), $data['dayOfWeek'], new DateTime($data['startTime']), new DateTime($data['endTime']), $id);

        if (!empty($existingScheduleItems))
            return $this->json(['error' => 'Time slot is already taken'], Response::HTTP_CONFLICT);

        $scheduleItemForUpdate->setSubject($subject);
        $scheduleItemForUpdate->setDayOfWeek($data['dayOfWeek']);
        $scheduleItemForUpdate->setStartTime(new DateTime($data['startTime']));
        $scheduleItemForUpdate->setEndTime(new DateTime($data['endTime']));

        $this->entityManager->persist($scheduleItemForUpdate);
        $this->entityManager->flush();

        return $this->json([$scheduleItemForUpdate], Response::HTTP_OK, [], ['groups' => ['schedule_item']]);
    }

    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/{id<\d+>}', methods: ['PATCH'])]
    public function patchScheduleItem(int $id, PatchScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        // startTime, endTime и dayOfWeek могут идти только вместе
        if ((isset($data['startTime']) || isset($data['endTime']) || isset($data['dayOfWeek']))
            && !(isset($data['startTime']) && isset($data['endTime']) && isset($data['dayOfWeek'])))
            return $this->json(['error' => 'startTime endTime and dayOfWeek need to be together ;('], Response::HTTP_BAD_REQUEST);

        $startTimeDT = new DateTime($data['startTime']);
        $endTimeDT = new DateTime($data['endTime']);
        if ($startTimeDT > $endTimeDT)
            return $this->json(['error' => 'startTime cannot be larger than endTime'], Response::HTTP_BAD_REQUEST);

        $scheduleItemForUpdate = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForUpdate)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

        if (isset($data['startTime']) && isset($data['endTime']) && isset($data['dayOfWeek'])) {
            // Проверка на пересечение расписания
            $existingScheduleItems = $this->entityManager
                ->getRepository(ScheduleItem::class)
                ->findOverlappingItems($scheduleItemForUpdate->getSchedule()->getId(), $data['dayOfWeek'], new DateTime($data['startTime']), new DateTime($data['endTime']), $id);

            if (!empty($existingScheduleItems))
                return $this->json(['error' => 'Time slot is already taken'], Response::HTTP_CONFLICT);
        }

        if (isset($data['subjectId'])) {
            $subject = $this->entityManager->getRepository(Subject::class)->find($data['subjectId']);
            if ($subject)
                $scheduleItemForUpdate->setSubject($subject);
        }

        if (isset($data['dayOfWeek']))
            $scheduleItemForUpdate->setDayOfWeek($data['dayOfWeek']);

        if (isset($data['startTime']))
            $scheduleItemForUpdate->setStartTime(new DateTime($data['startTime']));

        if (isset($data['endTime']))
            $scheduleItemForUpdate->setEndTime(new DateTime($data['endTime']));

        $this->entityManager->persist($scheduleItemForUpdate);
        $this->entityManager->flush();

        return $this->json($scheduleItemForUpdate, Response::HTTP_OK, [], ['groups' => ['schedule_item']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule_item/{id<\d+>}', methods: ['DELETE'])]
    public function deleteScheduleItem(int $id): JsonResponse
    {
        $scheduleItemForDelete = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForDelete)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

        $this->entityManager->remove($scheduleItemForDelete);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/schedule/create', methods: ['POST'])]
    public function createSchedule(CreateScheduleRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $existingSchedule = $this->entityManager->getRepository(Schedule::class)->findOneBy(['title' => $data['title']]);
        if ($existingSchedule)
            return $this->json(['error' => 'Schedule already exists'], Response::HTTP_CONFLICT);

        $schedule = new Schedule();
        $schedule->setTitle($data['title']);

        $this->entityManager->persist($schedule);
        $this->entityManager->flush();

        return $this->json([$schedule], Response::HTTP_CREATED);
    }
}
