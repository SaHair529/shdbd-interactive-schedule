<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Requests\GroupController\NewRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/group')]
class GroupController extends AbstractController
{
    private User $user;

    public function __construct(
            private readonly EntityManagerInterface $entityManager,
            TokenStorageInterface $tokenStorage
        )
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $groups = $this->entityManager->getRepository(Group::class)->findAll();
        return $this->json($groups, Response::HTTP_OK, [], ['groups' => ['group_list']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['POST'])]
    public function new(NewRequest $request, SerializerInterface $serializer): JsonResponse
    {
        $group = $serializer->deserialize($request->getRequest()->getContent(), Group::class, 'json');
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json($group, Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['DELETE'])]
    public function batchDelete(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];
        $ids = $requestData['ids'] ?? [];

        $this->entityManager->createQueryBuilder()
            ->delete(Group::class, 'g')
            ->where('g.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}