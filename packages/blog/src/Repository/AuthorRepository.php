<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Repository;

use Pehapkari\Blog\ValueObject\Author;
use Pehapkari\Exception\ShouldNotHappenException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class AuthorRepository
{
    /**
     * @var Author[]
     */
    private array $authors = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {
        foreach ($parameterBag->get('authors') as $id => $author) {
            $author = new Author($id, $author['name'], $author['photo'] ?? null);
            $this->authors[] = $author;
        }
    }

    /**
     * @return Author[]
     */
    public function fetchAll(): array
    {
        return $this->authors;
    }

    public function get(int $id): Author
    {
        foreach ($this->authors as $author) {
            if ($author->getId() !== $id) {
                continue;
            }

            return $author;
        }

        throw new ShouldNotHappenException(sprintf('Author for %d id was not found', $id));
    }

    public function getCount(): int
    {
        return count($this->fetchAll());
    }
}
