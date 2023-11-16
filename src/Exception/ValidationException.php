<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends BadRequestHttpException
{
    public function __construct(ConstraintViolationListInterface $violations, \Throwable $previous = null)
    {
        $message = '';

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $message .= "[{$violation->getPropertyPath()}] {$violation->getMessage()}\n";
        }

        parent::__construct($message, $previous, Response::HTTP_BAD_REQUEST);
    }
}