<?php

namespace App\Tests\User\Presentation\Controller;

use App\Tests\Utils\BaseWebTestCase;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class UserRegisterControllerTest extends BaseWebTestCase
{
    public function testCreateUserWithValidationError(): void
    {
        /** @var Router $router */
        $router = self::getContainer()->get('router');

        self::$client->request(
            'POST',
            $router->generate('register'),
            [
                'username' => '',
                'plainPassword' => '',
                'email' => ''
            ]
        );

        $response = json_decode(self::$client->getResponse()->getContent(), true);
        self::assertArrayHasKey('errors', $response);
        self::assertCount(3, $response['errors']);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);

        $expectedErrors = [
            'username' => 'Votre nom d\'utilisateur doit faire au moins 3 caractères',
            'plainPassword' => 'Votre mot de passe n\'est pas assez solid',
            'email' => 'Veuillez remplir un email',
        ];

        foreach ($response['errors'] as $error) {
            self::assertEquals($error['message'], $expectedErrors[$error['path']]);
        }
    }

    public function testCreateUserSuccessfully(): void
    {
        /** @var Router $router */
        $router = self::getContainer()->get('router');
        $userData = [
            'username' => 'lucky luciano',
            'plainPassword' => 'P4ssw@rd1234',
            'email' => 'lauren@luciano.mafia'
        ];

        self::$client->request('POST', $router->generate('register'), $userData);
        self::assertResponseIsSuccessful();

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $userData['email']
        ]);

        self::assertNotNull($user);
        self::assertSame($userData['username'], $user->getUsername());
        self::assertNull($user->getPlainPassword());
        self::assertSame($userData['email'], $user->getUserIdentifier());
    }

    public function testCreateUserSuccessfullyAndWithUniqueViolation(): void
    {
        /** @var Router $router */
        $router = self::getContainer()->get('router');
        $userData = [
            'username' => 'lucky luciano',
            'plainPassword' => 'P4ssw@rd1234',
            'email' => 'lauren@luciano.mafia'
        ];

        self::$client->request('POST', $router->generate('register'), $userData);
        self::$client->request('POST', $router->generate('register'), $userData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode(self::$client->getResponse()->getContent(), true);

        self::assertArrayHasKey('errors', $response);
        self::assertCount(1, $response['errors']);
        $error = $response['errors'][0];
        self::assertEquals('Un compte avec cette email existe déjà', $error['message']);
        self::assertEquals('email', $error['path']);
    }
}
