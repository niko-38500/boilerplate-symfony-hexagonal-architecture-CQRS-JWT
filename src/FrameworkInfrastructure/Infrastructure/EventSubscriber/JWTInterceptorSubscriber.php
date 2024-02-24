<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\EventSubscriber;

use App\User\Domain\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTInterceptorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['username'] = $user->getUsername();
        $payload['uuid'] = $user->getUuid();
        $payload['isAccountValidated'] = $user->isAccountValidated();

        $event->setData($payload);
    }
}
