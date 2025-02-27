<?php
namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/teacher')]
class TeacherController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/', methods: ['GET'])]
    public function list()
    {
        $teachers = array_filter($this->em->getRepository(User::class)->findAll(), function ($user) {
            return in_array(UserRole::ROLE_TEACHER, $user->getRoles());
        });
        
        return $this->json($teachers, Response::HTTP_OK, [], ['groups' => ['user_compact']]);
    }
}