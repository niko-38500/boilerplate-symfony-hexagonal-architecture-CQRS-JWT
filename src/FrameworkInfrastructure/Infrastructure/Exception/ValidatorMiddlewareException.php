<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorMiddlewareException extends \Exception implements HttpException
{
    /**
     * @param ConstraintViolationListInterface[] $violations
     */
    public function __construct(public readonly array $violations)
    {
        parent::__construct();
    }
}