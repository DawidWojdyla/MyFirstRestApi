<?php

declare(strict_types=1);

class ResponseBuilder
{

    public function getErrorResponse($message): string
    {
        return "{'error': '{$message}'}";
    }

    public function getSuccessResponse(): string
    {
        return "{'success': 'true'}";
    }
}