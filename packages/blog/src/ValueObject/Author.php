<?php

declare(strict_types=1);

namespace Pehapkari\Blog\ValueObject;

final class Author
{
    private int $id;

    private string $name;

    private ?string $photo = null;

    public function __construct(int $id, string $name, ?string $photo = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->photo = $photo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }
}
