<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Проверяем, что email и password переданы
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // Проверяем, нет ли уже такого пользователя
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['status' => 'error', 'message' => 'User with this email already exists'], Response::HTTP_CONFLICT);
        }

        // Создаём нового пользователя
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);

        // Хешируем пароль
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Сохраняем пользователя в базе данных
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}

