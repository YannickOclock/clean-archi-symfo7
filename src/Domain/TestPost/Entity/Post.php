<?php

namespace App\Domain\TestPost\Entity;

class Post {
    private $id;
    private $title;
    private $content;

    public function __construct(string $id, string $title, string $content) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getContent(): string {
        return $this->content;
    }
}