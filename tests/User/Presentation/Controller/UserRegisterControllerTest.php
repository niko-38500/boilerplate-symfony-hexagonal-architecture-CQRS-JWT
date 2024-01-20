<?php

namespace App\Tests\User\Presentation\Controller;

use App\Tests\Utils\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class UserRegisterControllerTest extends BaseWebTestCase
{
    public function testCreateUserWithValidationError(): void
    {
        self::$client->request(
            'POST',
            $this->router->generate('user_register'),
            [
                'username' => '',
                'plainPassword' => '',
                'email' => '',
            ]
        );

        $response = json_decode(self::$client->getResponse()->getContent(), true);
        self::assertArrayHasKey('errors', $response);
        self::assertCount(3, $response['errors']);
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response['code']);

        $expectedErrors = [
            'username' => 'Votre nom d\'utilisateur doit faire au moins 3 caractères',
            'plainPassword' => 'Votre mot de passe n\'est pas assez fort, il doit comporter des majuscules, des chiffres, des minuscules et des caractères spéciaux',
            'email' => 'Veuillez remplir un email',
        ];

        foreach ($response['errors'] as $error) {
            self::assertEquals($error['message'], $expectedErrors[$error['path']]);
        }
    }

    public function testCreateUserSuccessfullyAndWithUniqueViolation(): void
    {
        $userData = [
            'username' => 'lucky luciano',
            'plainPassword' => 'P4ss!w@rd12$3aGa4',
            'email' => 'lauren@luciano.mafia',
        ];

        self::$client->request('POST', $this->router->generate('user_register'), $userData);
        self::$client->request('POST', $this->router->generate('user_register'), $userData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = json_decode(self::$client->getResponse()->getContent(), true);

        self::assertArrayHasKey('errors', $response);
        self::assertCount(1, $response['errors']);
        $error = $response['errors'][0];
        self::assertEquals('Un compte avec cette email existe déjà', $error['message']);
        self::assertEquals('email', $error['path']);
    }
}
