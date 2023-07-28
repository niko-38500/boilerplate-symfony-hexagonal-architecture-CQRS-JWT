<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $uuid;
    private string $password;
    private bool $isAccountValidated = false;
    private ?string $plainPassword = null;

    public function __construct(
        private readonly string $username,
        private readonly string $email,
        string $plainPassword,
    ) {
        $this->uuid = Uuid::uuid4()->toString();
        $this->plainPassword = $plainPassword;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function validateAccount(): void
    {
        $this->isAccountValidated = true;
    }

    public function isAccountValidated(): bool
    {
        return $this->isAccountValidated();
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $newHashedPassword): self
    {
        $this->password = $newHashedPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}