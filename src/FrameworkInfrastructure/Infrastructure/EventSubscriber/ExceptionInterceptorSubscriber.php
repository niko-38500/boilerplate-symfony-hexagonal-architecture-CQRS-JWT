<?php

declare(strict_types=1);

namespace App\FrameworkInfrastructure\Infrastructure\EventSubscriber;

use App\FrameworkInfrastructure\Domain\Exception\HttpException;
use App\FrameworkInfrastructure\Domain\Exception\NotFoundException;
use App\FrameworkInfrastructure\Infrastructure\Exception\ValidatorMiddlewareException;
use App\FrameworkInfrastructure\Infrastructure\ValueObject\ViolationItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ExceptionInterceptorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HttpException) {
            $event->setResponse(new JsonResponse([
                'createdAt' => time(),
                'status' => 'error',
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ]));
            return;
        }

        if ($exception instanceof NotFoundException) {
            $event->setResponse(new JsonResponse($exception->getResponseBody(), Response::HTTP_NOT_FOUND));
        } elseif ($exception instanceof ValidatorMiddlewareException) {
            $event->setResponse(new JsonResponse([
                'createdAt' => time(),
                'status' => 'error',
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $this->formatViolationList($exception->violations)
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    /**
     * @param ConstraintViolationListInterface[] $violations
     *
     * @return ViolationItem[]
     */
    private function formatViolationList(array $violations): array
    {
        $formattedViolations = [];

        foreach ($violations as $violationList) {
            foreach ($violationList as $violation) {
                $formattedViolations[] = new ViolationItem(
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
        }

        return $formattedViolations;
    }
}