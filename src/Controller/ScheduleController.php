<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ScheduleItem;
use App\Entity\Subject;
use App\Requests\ScheduleController\AddScheduleItemRequest;
use App\Requests\ScheduleController\PatchScheduleItemRequest;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ScheduleController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/schedule', methods: ['GET'])]
    public function getSchedule(): JsonResponse
    {
        $scheduleItems = $this->entityManager->getRepository(ScheduleItem::class)->findAll();

        return $this->json($scheduleItems, Response::HTTP_OK, [], ['groups' => ['schedule_item']]);
    }

    /**
     * @throws Exception
     */
    #[Route('/schedule', methods: ['POST'])]
    public function addScheduleItem(AddScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $subject = $this->entityManager->getRepository(Subject::class)->find($data['subjectId']);
        if (!$subject)
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);

        $scheduleItem = new ScheduleItem();
        $scheduleItem->setDayOfWeek($data['dayOfWeek']);
        $scheduleItem->setSubject($subject);
        $scheduleItem->setStartTime(new DateTime($data['startTime']));
        $scheduleItem->setEndTime(new DateTime($data['endTime']));
        $scheduleItem->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($scheduleItem);
        $this->entityManager->flush();

        return $this->json($scheduleItem, Response::HTTP_CREATED, [], ['groups' => ['schedule_item']]);
    }

    /**
     * @throws Exception
     */
    #[Route('/schedule/{id<\d+>}', methods: ['PUT'])]
    public function updateScheduleItem(int $id, AddScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $scheduleItemForUpdate = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForUpdate)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

        $subject = $this->entityManager->getRepository(Subject::class)->find($data['subjectId']);
        if (!$subject)
            return $this->json(['error' => 'Subject not found'], Response::HTTP_NOT_FOUND);

        $scheduleItemForUpdate->setSubject($subject);
        $scheduleItemForUpdate->setDayOfWeek($data['dayOfWeek']);
        $scheduleItemForUpdate->setStartTime(new DateTime($data['startTime']));
        $scheduleItemForUpdate->setEndTime(new DateTime($data['endTime']));

        $this->entityManager->persist($scheduleItemForUpdate);
        $this->entityManager->flush();

        return $this->json([$scheduleItemForUpdate], Response::HTTP_OK);
    }

    /**
     * @throws Exception
     */
    #[Route('/schedule/{id<\d+>}', methods: ['PATCH'])]
    public function patchScheduleItem(int $id, PatchScheduleItemRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true) ?? [];

        $scheduleItemForUpdate = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForUpdate)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

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

    #[Route('/schedule/{id<\d+>}', methods: ['DELETE'])]
    public function deleteScheduleItem(int $id): JsonResponse
    {
        $scheduleItemForDelete = $this->entityManager->getRepository(ScheduleItem::class)->find($id);
        if (!$scheduleItemForDelete)
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);

        $this->entityManager->remove($scheduleItemForDelete);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
