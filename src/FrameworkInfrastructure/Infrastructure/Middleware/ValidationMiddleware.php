<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Middleware;

use App\FrameworkInfrastructure\Infrastructure\Exception\ValidatorMiddlewareException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $items = $envelope->getMessage();
        $errors = [];

        foreach ($items as $item) {
            if (!is_object($item)) {
                continue;
            }

            $errorsHandler = $this->validator->validate($item);
            if ($errorsHandler->count()) {
                $errors[] = $errorsHandler;
            }
        }

        if (count($errors)) {
            throw new ValidatorMiddlewareException($errors);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}