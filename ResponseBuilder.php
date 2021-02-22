<?php

declare(strict_types=1);

class ResponseBuilder
{

    public static function getErrorResponse($message): string
    {
        return "{'error': '{$message}'}";
    }

    public static function getSuccessResponse(): string
    {
        return "{'success': 'true'}";
    }
}