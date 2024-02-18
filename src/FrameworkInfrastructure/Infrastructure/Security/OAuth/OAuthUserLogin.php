<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth;

use App\FrameworkInfrastructure\Domain\Repository\PersisterManagerInterface;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;

class OAuthUserLogin
{
    /** @var array<string, string>  */
    public const array PROVIDER_ID_PROPERTY_PATH = [
        'github' => 'githubId'
    ];

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PersisterManagerInterface $persisterManager,
    ) {}

    public function loginUser(ResourceOwnerDTO $resourceOwner): User
    {
        if (!array_key_exists($resourceOwner->currentProvider, self::PROVIDER_ID_PROPERTY_PATH)) {
            throw new \RuntimeException(sprintf(
                'To use the %s provider you have to define the property to fetch in the class %s',
                $resourceOwner->currentProvider,
                self::class
            ));
        }

        if (!property_exists(User::class, self::PROVIDER_ID_PROPERTY_PATH[$resourceOwner->currentProvider])) {
            throw new \RuntimeException(sprintf(
                'Property %s does not exists on class %s. please verify that %s::PROVIDER_ID_PROPERTY_PASS has the right property value for the key %s',
                self::PROVIDER_ID_PROPERTY_PATH[$resourceOwner->currentProvider],
                User::class,
                self::class,
                $resourceOwner->currentProvider
            ));
        }

        $user = $this->getUserFromProviderId($resourceOwner) ?? $this->getUserFromProviderEmail($resourceOwner);

        if ($user) {
            return $user;
        }

        return $this->createUserFromResourceOwner($resourceOwner);
    }

    private function createUserFromResourceOwner(ResourceOwnerDTO $resourceOwner): User
    {
        $user = (new User($resourceOwner->username, $resourceOwner->email))
            ->setProviderId(
                self::PROVIDER_ID_PROPERTY_PATH[$resourceOwner->currentProvider],
                $resourceOwner->providerId
            )
            ->validateAccount();

        $this->persisterManager->save($user, true);

        return $user;
    }

    private function getUserFromProviderEmail(ResourceOwnerDTO $resourceOwner): ?User
    {
        $user = $this->userRepository->findOneByEmail($resourceOwner->email);

        if ($user) {
            $user
                ->setProviderId(
                    self::PROVIDER_ID_PROPERTY_PATH[$resourceOwner->currentProvider],
                    $resourceOwner->providerId
                )
                ->validateAccount();

            $this->persisterManager->save($user, true);
        }

        return $user;
    }

    private function getUserFromProviderId(ResourceOwnerDTO $resourceOwner): ?User
    {
        $propertyPass = self::PROVIDER_ID_PROPERTY_PATH[$resourceOwner->currentProvider];

        return $this->userRepository->findOneByAuthenticatorProviderId($propertyPass, $resourceOwner->providerId);
    }
}