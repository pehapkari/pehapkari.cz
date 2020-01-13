<?php

declare(strict_types=1);

namespace Pehapkari\Blog\ValueObject;

final class AuthorPosts
{
    /**
     * @var string
     */
    private $authorName;

    /**
     * @var int
     */
    private $postCount;

    /**
     * @var string|null
     */
    private $authorPhoto;

    /**
     * @var int
     */
    private $postsWordCount;

    public function __construct(string $authorName, ?string $authorPhoto, int $postCount, int $postsWordCount)
    {
        $this->authorName = $authorName;
        $this->postCount = $postCount;
        $this->authorPhoto = $authorPhoto;
        $this->postsWordCount = $postsWordCount;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getPostCount(): int
    {
        return $this->postCount;
    }

    public function getAuthorPhoto(): ?string
    {
        return $this->authorPhoto;
    }

    public function getPostsWordCount(): int
    {
        return $this->postsWordCount;
    }
}
