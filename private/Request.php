<?php

declare(strict_types=1);

class Request
{
    private array $get;
    private array $post;

    public function __construct(array $get, array $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function getAction(): string
    {
        return $this->get['action'] ?? '';
    }

    public function getPostData(): array
    {
        return $this->post;
    }
}