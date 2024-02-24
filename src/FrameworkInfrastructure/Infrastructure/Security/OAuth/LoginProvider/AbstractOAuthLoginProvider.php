<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider;

use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @template T of ResourceOwnerInterface
 */
abstract class AbstractOAuthLoginProvider
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PersisterManagerInterface $persisterManager,
    ) {}

    abstract public static function getUserPropertyPathForProvider(): string;

    abstract public static function getCurrentAuthenticationProvider(): string;

    /**
     * @param T $resourceOwner
     */
    public function loginUser(ResourceOwnerInterface $resourceOwner): User
    {
        $this->validateUserBeforeAuthenticate();

        $resourceOwner = $this->transformClientResourceOwnerIntoDTO($resourceOwner);

        $user = $this->getUserFromProviderId($resourceOwner) ?? $this->getUserFromProviderEmail($resourceOwner);

        if ($user) {
            return $user;
        }

        return $this->createUserFromResourceOwner($resourceOwner);
    }

    public function validateUserBeforeAuthenticate(): void
    {
        if (!property_exists(User::class, static::getUserPropertyPathForProvider())) {
            throw new \RuntimeException(sprintf(
                'Property %s does not exists on class %s. please verify that %s::getUserPropertyPathForProvider() '.
                'return the proper property name for the %s provider',
                static::getUserPropertyPathForProvider(),
                User::class,
                static::class,
                static::getCurrentAuthenticationProvider()
            ));
        }
    }

    /**
     * @param T $resourceOwner
     */
    abstract protected function transformClientResourceOwnerIntoDTO(
        ResourceOwnerInterface $resourceOwner
    ): ResourceOwnerDTO;

    private function createUserFromResourceOwner(ResourceOwnerDTO $resourceOwner): User
    {
        $user = (new User($resourceOwner->username, $resourceOwner->email))
            ->setProviderId(
                static::getUserPropertyPathForProvider(),
                $resourceOwner->providerId
            )
            ->validateAccount()
        ;

        $this->persisterManager->save($user, true);

        return $user;
    }

    private function getUserFromProviderEmail(ResourceOwnerDTO $resourceOwner): ?User
    {
        $user = $this->userRepository->findOneByEmail($resourceOwner->email);

        if ($user) {
            $user
                ->setProviderId(
                    static::getUserPropertyPathForProvider(),
                    $resourceOwner->providerId
                )
                ->validateAccount()
            ;

            $this->persisterManager->save($user, true);
        }

        return $user;
    }

    private function getUserFromProviderId(ResourceOwnerDTO $resourceOwner): ?User
    {
        return $this->userRepository->findOneByAuthenticatorProviderId(
            static::getUserPropertyPathForProvider(),
            $resourceOwner->providerId
        );
    }
}
