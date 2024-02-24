<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface // TODO find a way to decoupling the user entity from the external packages
{
    private string $uuid;
    private ?string $password;
    private bool $isAccountValidated = false;
    private ?string $plainPassword;
    private ?string $emailVerificationToken;
    private ?string $githubId;

    public function __construct(
        private readonly string $username,
        private readonly string $email,
        ?string $plainPassword = null,
    ) {
        $this->uuid = Uuid::uuid4()->toString();
        $this->plainPassword = $plainPassword;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function setProviderId(string $providerIdProperty, string $providerId): self
    {
        $this->{$providerIdProperty} = $providerId;

        return $this;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): void
    {
        $this->emailVerificationToken = $emailVerificationToken;
    }

    public function validateAccount(): self
    {
        $this->isAccountValidated = true;

        return $this;
    }

    public function isAccountValidated(): bool
    {
        return $this->isAccountValidated;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    /**
     * @param string                $username
     * @param array<string, string> $payload
     */
    public static function createFromPayload($username, array $payload): self
    {
        $user = (new self($payload['username'], $payload['email']))->setUuid($payload['uuid']);

        if ($payload['isAccountValidated']) {
            $user->validateAccount();
        }

        return $user;
    }
}
