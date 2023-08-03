<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends \Exception implements HttpException
{
    /**
     * @return array<string, int|string>
     */
    public function getResponseBody(): array
    {
        return [
            'createdAt' => time(),
            'status' => 'Not found',
            'code' => Response::HTTP_NOT_FOUND,
        ];
    }
}
