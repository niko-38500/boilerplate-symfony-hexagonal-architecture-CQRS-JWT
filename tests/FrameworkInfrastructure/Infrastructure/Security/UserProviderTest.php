<?php

namespace App\Tests\FrameworkInfrastructure\Infrastructure\Security;

use App\FrameworkInfrastructure\Infrastructure\Security\UserProvider;
use App\Tests\Utils\BaseWebTestCase;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 *
 * @coversNothing
 */
class UserProviderTest extends BaseWebTestCase
{
    private UserProvider $userProvider;

    public function setUp(): void
    {
        parent::setUp();

        /** @var UserProvider $userProvider */
        $userProvider = static::getContainer()->get(UserProvider::class);
        $this->userProvider = $userProvider;
    }

    public function testGetAuthenticatedUser(): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => 'john@doe.fr',
        ]);

        self::$client->request('POST', '/api/login_check', [], [], [], json_encode([
            'email' => $user->getEmail(),
            'password' => 'root',
        ]));

        $responseData = json_decode(self::$client->getResponse()->getContent(), true);

        self::assertArrayHasKey('token', $responseData);

        $actualUser = $this->userProvider->getAuthenticatedUser();

        self::assertSame($user, $actualUser);
    }

    public function testGetAuthenticatedUserWithUnverifiedEmail(): void
    {
        self::$client->request('POST', '/api/login_check', [], [], [], json_encode([
            'email' => 'unverified@mail.fr',
            'password' => 'root',
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
