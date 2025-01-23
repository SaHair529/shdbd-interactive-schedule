<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Schedule;
use App\Entity\User;
use App\Requests\UserController\BatchAddGroupRequest;
use App\Requests\UserController\NewRequest;
use App\Requests\UserController\UpdateRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user')]
class UserController extends AbstractController
{
    private User $user;

    public function __construct(private readonly EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, private UserPasswordHasherInterface $passwordHasher)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id<\d+>}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user =  $this->entityManager->getRepository(User::class)->find($id);
        if ($user === null) {
            return $this->json(['error' => 'not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 25);
        $searchQuery = $request->get('searchQuery', '');
        $filter = json_decode($request->query->get('filter'), true);

        $totalUsersQuery = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');

        if (!empty($searchQuery)) {
            $totalUsersQuery->where('u.fullName LIKE :search OR u.email LIKE :search')
                             ->setParameter('search', '%' . $searchQuery . '%');
        }

        $totalUsers = $totalUsersQuery->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();

        $usersQuery = $this->entityManager->getRepository(User::class)->createQueryBuilder('u');

        if (!empty($searchQuery)) {
            $usersQuery->where('u.fullName LIKE :search OR u.email LIKE :search')
                        ->setParameter('search', '%' . $searchQuery . '%');
        }

        if (!empty($filter['groups'])) {
            $usersQuery->innerJoin('u.groups', 'g')
                        ->andWhere('g.id IN (:groups)')
                        ->setParameter('groups', $filter['groups']);
        }

        $users = $usersQuery->setFirstResult(($page - 1) * $limit)
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
        ], Response::HTTP_OK, [], ['groups' => ['user_compact']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['POST'])]
    public function new(NewRequest $request): JsonResponse
    {
        $requestData = json_decode($request->getRequest()->getContent(), true);

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $requestData['email']]);
        if ($existingUser !== null) {
            return $this->json(['status' => 'error', 'message' => 'User already exists.'], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($requestData['email']);
        $user->setFullName($requestData['fullName']);
        $user->setRoles([$requestData['role']]);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $requestData['password']);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['status' => 'ok', 'user' => $user], Response::HTTP_CREATED, [], ['groups' => ['user_compact']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['DELETE'])]
    public function batchDelete(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true) ?? [];
        $ids = $requestData['ids'] ?? [];

        $this->entityManager->createQueryBuilder()
            ->delete(User::class, 'u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Добавление пользователей в группу
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/add_group', methods: ['POST'])]
    public function batchAddGroup(BatchAddGroupRequest $request): JsonResponse
    {
        $requestData = json_decode($request->getRequest()->getContent(), true);
        $filteredUsersIds = array_filter((array) $requestData['usersIds'], fn($value) => is_numeric($value));

        $group = $this->entityManager->getRepository(Group::class)->find($requestData['groupId']);
        $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $filteredUsersIds]);

        foreach ($users as $user) {
            $group->addParticipant($user);
        }

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['success' => 'ok'], Response::HTTP_OK);
    }

    /**
     * Удаление пользователей из группы
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/remove_group', methods: ['POST'])]
    public function batchRemoveGroup(BatchAddGroupRequest $request): JsonResponse
    {
        $requestData = json_decode($request->getRequest()->getContent(), true);
        $filteredUsersIds = array_filter((array) $requestData['usersIds'], fn($value) => is_numeric($value));

        $group = $this->entityManager->getRepository(Group::class)->find($requestData['groupId']);
        $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $filteredUsersIds]);

        foreach($users as $user) {
            $group->removeParticipant($user);
        }

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $this->json(['success' => 'ok'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id<\d+>}', methods: ['POST'])]
    public function update(int $id, UpdateRequest $request): JsonResponse
    {
        $requestData = json_decode($request->getRequest()->getContent(), true);

        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'user not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($requestData['email'])) {
            $userByEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $requestData['email']]);
            if ($userByEmail && $userByEmail->getId() !== $id) {
                return $this->json(['error' => 'Email is not available']);
            }
        }

        $user->setFullName($requestData['fullName'] ?? $user->getFullName());
        $user->setEmail($requestData['email'] ?? $user->getEmail());

        if (isset($requestData['roles'])){
            $user->setRoles($requestData['roles']);
        }

        if (isset($requestData['groups'])) {
            foreach ($user->getGroups() as $group) {
                $user->removeGroup($group);
            }

            $groups = $this->entityManager->getRepository(Group::class)->findBy(['id' => array_column($requestData['groups'], 'id')]);
            foreach ($groups as $group) {
                $user->addGroup($group);
            }
        }

        if (isset($requestData['schedules'])) {
            foreach ($user->getSchedules() as $schedule) {
                $user->removeSchedule($schedule);
            }

            $schedules = $this->entityManager->getRepository(Schedule::class)->findBy(['id' => array_column($requestData['schedules'], 'id')]);
            foreach ($schedules as $schedule) {
                $user->addSchedule($schedule);
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user']);
    }
}