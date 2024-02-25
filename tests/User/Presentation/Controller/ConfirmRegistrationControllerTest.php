<?php

namespace App\Tests\User\Presentation\Controller;

use App\Tests\Utils\BaseWebTestCase;
use App\User\Infrastructure\Email\UserRegistrationConfirmationEmail;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class ConfirmRegistrationControllerTest extends BaseWebTestCase
{
    public function testPageNotFoundWhenNoToken(): void
    {
        self::$client->request('GET', $this->router->generate('user_account_validation'));
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testNotFoundOnNotExistingToken(): void
    {
        self::$client->request('GET', $this->router->generate('user_account_validation', [
            'token' => 'fake_token',
        ]));
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testNotFoundOnExpiredToken(): void
    {
        self::$client->request('POST', $this->router->generate('user_register'), [
            'username' => 'johnnyS',
            'plainPassword' => 'StandardP4ssw@rd!',
            'email' => 'johnny@s.fr',
        ]);

        self::assertQueuedEmailCount(1);

        /** @var UserRegistrationConfirmationEmail $email */
        $email = self::getMailerMessage();

        preg_match('#<a .*href=".*?token=(.+)?"#', $email->getHtmlBody(), $matches);
        self::assertArrayHasKey(1, $matches);

        $token = $matches[1];

        $tokenExpirationTime = self::getContainer()->getParameter('email_confirmation_token_expiration_delay');
        CarbonImmutable::setTestNow(new CarbonImmutable(sprintf('now + %s minutes', $tokenExpirationTime)));
        self::$client->request('GET', $this->router->generate('user_account_validation', [
            'token' => $token,
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        CarbonImmutable::setTestNow();
    }
}
