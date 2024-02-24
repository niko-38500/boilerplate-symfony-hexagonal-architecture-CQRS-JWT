<?php

namespace App\Tests\FrameworkInfrastructure\Infrastructure\Security\OAuth;

use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\OAuthUserLogin;
use App\Tests\Utils\BaseKernelTestCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;

/**
 * @internal
 */
class OAuthUserLoginTest extends BaseKernelTestCase
{
    private OAuthUserLogin $oAuthUserLogin;

    public function setUp(): void
    {
        parent::setUp();

        $this->oAuthUserLogin = self::getContainer()->get(OAuthUserLogin::class);
    }

    public function testRetrieveUserFromProviderId(): void
    {
        $user = new User(
            'johnnyD',
            'johnny@doe.com'
        );
        $user
            ->setProviderId('githubId', '1')
            ->validateAccount()
        ;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $resourceOwner = new ResourceOwnerDTO(
            'johnnyD',
            'john@doe.io',
            '1',
            'github'
        );

        $updatedUser = $this->oAuthUserLogin->loginUser($resourceOwner);

        self::assertEquals($user->getEmail(), $updatedUser->getEmail());
        self::assertEquals('1', $user->getGithubId());
        self::assertEquals($user->getUuid(), $updatedUser->getUuid());
    }

    public function testRetrieveUserFromEmail(): void
    {
        $user = new User(
            'johnnyD',
            'johnny@doe.com'
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $resourceOwner = new ResourceOwnerDTO(
            'johnnyD',
            'johnny@doe.com',
            '1',
            'github'
        );

        $updatedUser = $this->oAuthUserLogin->loginUser($resourceOwner);

        self::assertTrue($updatedUser->isAccountValidated());
        self::assertEquals('1', $updatedUser->getGithubId());
        self::assertEquals($user->getUuid(), $updatedUser->getUuid());
    }

    public function testUserIsCreatedWhenNotExists(): void
    {
        $resourceOwner = new ResourceOwnerDTO(
            'johnnyD',
            'johnny@doe.com',
            '1',
            'github'
        );

        $newUser = $this->oAuthUserLogin->loginUser($resourceOwner);

        self::assertTrue($newUser->isAccountValidated());
        self::assertEquals('1', $newUser->getGithubId());

        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $fetchedUser = $userRepository->findOneByEmail('johnny@doe.com');

        self::assertEquals($newUser, $fetchedUser);
    }

    public function testExceptionOnNonExistingProvider(): void
    {
        $resourceOwner = new ResourceOwnerDTO(
            'johnnyD',
            'johnny@doe.com',
            '1',
            'not-exists'
        );

        $this->expectException(\RuntimeException::class);

        $this->oAuthUserLogin->loginUser($resourceOwner);
    }
}
