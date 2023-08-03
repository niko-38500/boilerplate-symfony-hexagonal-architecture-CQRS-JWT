<?php

namespace App\Tests\User\Presentation\Controller;

use App\Tests\Utils\BaseWebTestCase;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Infrastructure\Email\UserRegistrationConfirmationEmail;

/**
 * @internal
 *
 * @coversNothing
 */
class UserRegistrationFullWorkflowTest extends BaseWebTestCase
{
    public function testRegistrationWorkflow(): void
    {
        $userData = [
            'username' => 'lucky luciano',
            'plainPassword' => 'P4ssw@rd1234',
            'email' => 'lauren@luciano.mafia',
        ];

        self::$client->request('POST', $this->router->generate('user_register'), $userData);
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

        /** @var UserRegistrationConfirmationEmail $email */
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

        // TODO: add test that the temporary token is removed and the user has null token field
    }
}
