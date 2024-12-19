<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

    public function list()
    {
        $groups = $this->entityManager->getRepository(Group::class)->findAll();
        return $this->json($groups, Response::HTTP_OK, [], ['groups' => ['group_list']]);
    }
}