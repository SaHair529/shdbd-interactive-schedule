<?php

namespace App\EventListener;

use App\Entity\AccessToken;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccessTokenChecker
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
//        if (in_array($request->getPathInfo(), ['/api/login', '/api/register'])) { todo uncomment
//            return;
//        }

        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader && preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $tokenValue = $matches[1];

            $accessToken = $this->entityManager->getRepository(AccessToken::class)->findOneBy(['value' => $tokenValue]);
            if (!$accessToken)
                throw new AuthenticationException('Invalid access token');

            if ($accessToken->isRevoked())
                throw new AuthenticationException('Access token revoked');

            if ((new DateTimeImmutable()) > $accessToken->getExpiresAt())
                throw new AuthenticationException('Access token expired');
        }
        else {
            throw new AuthenticationException('Authorization header not found');
        }
    }
}