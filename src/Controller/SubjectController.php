<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subject;
use App\Requests\SubjectController\AddSubjectRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class SubjectController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/subject', methods: ['POST'])]
    public function addSubject(AddSubjectRequest $request): JsonResponse
    {
        $request = json_decode($request->getRequest()->getContent(), true);

        $existingSubject = $this->entityManager->getRepository(Subject::class)->findOneBy(['name' => $request['name']]);
        if ($existingSubject)
            return $this->json(['message' => 'Subject with the same name already exists', 'existing_subject' => $existingSubject], Response::HTTP_BAD_REQUEST);

        $subject = new Subject();
        $subject->setName($request['name']);

        $this->entityManager->persist($subject);
        $this->entityManager->flush();

        return $this->json($subject, Response::HTTP_CREATED, [], [
            'circular_reference_handler' => fn ($object) => $object->getId(),
        ]);
    }

    #[Route('/subject/list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $subjects = $this->entityManager->getRepository(Subject::class)->findAll();
        return $this->json($subjects, Response::HTTP_OK, [], ['groups' => ['subject']]);
    }
}
