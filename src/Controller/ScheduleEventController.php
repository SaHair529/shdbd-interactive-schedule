<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ScheduleEvent;
use App\Entity\ScheduleItem;
use App\Entity\User;
use App\Enum\EventType;
use App\Requests\ScheduleEventController\NewRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api')]
class ScheduleEventController extends AbstractController
{
    private User $user;

    public function __construct(private readonly EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[Route('/schedule/event', name: 'schedule_event_new', methods: ['POST'])]
    public function new(NewRequest $request): JsonResponse
    {
        $data = json_decode($request->getRequest()->getContent(), true);

        $scheduleItem = $this->entityManager->getRepository(ScheduleItem::class)->find($data['scheduleItemId']);
        $eventType = EventType::from($data['type']);

        $event = new ScheduleEvent();
        $event->setStudent($this->user);
        $event->setScheduleItem($scheduleItem);
        $event->setReason($data['reason']);
        $event->setType($eventType);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->json($event, Response::HTTP_CREATED, [], ['groups' => ['schedule_event']]);
    }

    #[Route('/schedule/event/list/{scheduleItemId<\d+>}', methods: ['GET'])]
    public function list(int $scheduleItemId): JsonResponse
    {
        $scheduleItem = $this->entityManager->getRepository(ScheduleItem::class)->find($scheduleItemId);
        if (!$scheduleItem) {
            return $this->json(['error' => 'Schedule item not found'], Response::HTTP_NOT_FOUND);
        }

	    return $this->json($scheduleItem->getScheduleEvents(), Response::HTTP_OK, [], ['groups' => ['schedule_event']]);
    }

    #[Route('/schedule/event/{eventId<\d+>}', methods: ['DELETE'])]
    public function delete(int $eventId): JsonResponse
    {
        $this->entityManager->createQueryBuilder()
            ->delete(ScheduleEvent::class, 'e')
            ->where('e.id = :eventId')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->execute();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
