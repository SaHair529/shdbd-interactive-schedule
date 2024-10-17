<?php

namespace App\EventListener;

use App\Entity\AccessToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccessTokenChecker
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (in_array($event->getRequest()->getPathInfo(), ['/api/login', '/api/register'])) {
            return;
        }

        $request = $event->getRequest();

        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $tokenValue = $matches[1];

            $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy(['value' => $tokenValue]);
            if (!$accessToken) {
                $event->setResponse(new JsonResponse([
                    'status' => 'error',
                    'message' => 'Invalid access token',
                ], Response::HTTP_UNAUTHORIZED));
                return;
            }

            if ($accessToken->isRevoked()) {
                $event->setResponse(new JsonResponse([
                    'status' => 'error',
                    'message' => 'Access token is revoked',
                ], Response::HTTP_UNAUTHORIZED));
                return;
            }

            if ((new DateTimeImmutable()) > $accessToken->getExpiresAt()) {
                $event->setResponse(new JsonResponse([
                    'status' => 'error',
                    'message' => 'Access token expired',
                ], Response::HTTP_UNAUTHORIZED));
            }

            $user = $accessToken->getOwner();
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
        }
        else {
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => 'Access token expired',
            ], Response::HTTP_UNAUTHORIZED));
        }
    }
}