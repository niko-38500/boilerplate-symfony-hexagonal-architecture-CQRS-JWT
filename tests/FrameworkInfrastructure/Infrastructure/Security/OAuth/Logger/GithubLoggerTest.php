<?php

namespace App\Tests\FrameworkInfrastructure\Infrastructure\Security\OAuth\Logger;

use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\LoginProvider\GithubLoginProvider;
use App\Tests\Utils\BaseKernelTestCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use League\OAuth2\Client\Provider\GithubResourceOwner;

/**
 * @internal
 */
class GithubLoggerTest extends BaseKernelTestCase
{
    private GithubLoginProvider $oAuthUserLogin;

    public function setUp(): void
    {
        parent::setUp();

        $this->oAuthUserLogin = self::getContainer()->get(GithubLoginProvider::class);
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

        $resourceOwner = $this->createResourceOwner(1, 'john@doe.io', 'johnnyD');

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

        $resourceOwner = $this->createResourceOwner(1, 'johnny@doe.com', 'johnnyD');

        $updatedUser = $this->oAuthUserLogin->loginUser($resourceOwner);

        self::assertTrue($updatedUser->isAccountValidated());
        self::assertEquals('1', $updatedUser->getGithubId());
        self::assertEquals($user->getUuid(), $updatedUser->getUuid());
    }

    public function testUserIsCreatedWhenNotExists(): void
    {
        $resourceOwner = $this->createResourceOwner(1, 'johnny@doe.com', 'johnnyD');

        $newUser = $this->oAuthUserLogin->loginUser($resourceOwner);

        self::assertTrue($newUser->isAccountValidated());
        self::assertEquals('1', $newUser->getGithubId());

        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $fetchedUser = $userRepository->findOneByEmail('johnny@doe.com');

        self::assertEquals($newUser, $fetchedUser);
    }

    private function createResourceOwner(int $id, string $email, string $nickname): GithubResourceOwner
    {
        $resourceOwner = $this->createMock(GithubResourceOwner::class);

        $resourceOwner->expects(self::once())->method('getId')->willReturn($id);
        $resourceOwner->expects(self::once())->method('getEmail')->willReturn($email);
        $resourceOwner->expects(self::once())->method('getNickname')->willReturn($nickname);

        return $resourceOwner;
    }
}
