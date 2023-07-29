<?php

namespace App\Tests\User\Presentation\Controller;

use App\Tests\Utils\BaseWebTestCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\NotificationAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use function PHPUnit\Framework\assertArrayHasKey;

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

    public function testRegistrationWorkflow(): void
    {
        self::$client->disableReboot();
        /** @var Router $router */
        $router = self::getContainer()->get('router');
        $userData = [
            'username' => 'lucky luciano',
            'plainPassword' => 'P4ssw@rd1234',
            'email' => 'lauren@luciano.mafia'
        ];

        self::$client->request('POST', $router->generate('register'), $userData);
        self::assertResponseIsSuccessful();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $user = $userRepository->findOneByEmail($userData['email']);

        self::assertNotNull($user);
        self::assertNotNull($user->getEmailVerificationToken());
        self::assertSame($userData['username'], $user->getUsername());
        self::assertNull($user->getPlainPassword());
        self::assertSame($userData['email'], $user->getUserIdentifier());
        self::assertFalse($user->isAccountValidated());

        self::assertQueuedEmailCount(1);

        $email = self::getMailerMessage();

        self::assertEmailAddressContains($email, 'to', $userData['email']);
        self::assertEmailHeaderSame($email, 'subject', 'Veuillez confirmer votre compte');
        self::assertEmailTextBodyContains(
            $email,
            sprintf(
                '(Ce lien ne sera plus valide à partir du %s)',
                (new \DateTimeImmutable('now + 15 minutes'))->format('d/m/Y à H:i')
            )
        );

        preg_match('#<a .*href="(.+)?"#', $email->getHtmlBody(), $matches);
        self::assertArrayHasKey(1, $matches);

        $confirmationLink = $matches[1];

        $emailVerificationToken = $user->getEmailVerificationToken();

        self::$client->request('GET', $confirmationLink);
        self::assertResponseIsSuccessful();

        $user = $userRepository->findOneByEmail($userData['email']);

        self::assertTrue($user->isAccountValidated());

        $response = json_decode(self::$client->getResponse()->getContent(), true);

        self::assertNull($userRepository->findOneByTemporaryToken($emailVerificationToken));

        self::assertArrayHasKey('token', $response['data']);
        self::assertNotEmpty($response['data']['token']);
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
