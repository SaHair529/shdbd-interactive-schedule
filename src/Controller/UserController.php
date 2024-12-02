<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
class UserController extends AbstractController
{
    private User $user;

    public function __construct(private readonly EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 25);

        $totalUsers = $this->entityManager->getRepository(User::class)->count();

        $users = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->setfirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalPages = ceil($totalUsers / $limit);

        return $this->json([
            'users' => $users,
            'meta' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_users' => $totalUsers,
                'total_pages' => $totalPages,
            ]
        ], Response::HTTP_OK, [], ['groups' => ['user']]);
    }
}