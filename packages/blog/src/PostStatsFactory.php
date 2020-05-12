<?php

declare(strict_types=1);

namespace Pehapkari\Blog;

use Pehapkari\Blog\Repository\AuthorRepository;
use Pehapkari\Blog\Repository\PostRepository;
use Pehapkari\Blog\ValueObject\AuthorPosts;
use Pehapkari\Blog\ValueObject\Post;

final class PostStatsFactory
{
    private PostRepository $postsProvider;

    private AuthorRepository $authorsProvider;

    public function __construct(PostRepository $postRepository, AuthorRepository $authorRepository)
    {
        $this->postsProvider = $postRepository;
        $this->authorsProvider = $authorRepository;
    }

    /**
     * @return AuthorPosts[]
     */
    public function create(): array
    {
        $authorsPostsData = $this->createPostsByAuthor();

        $authorsPosts = $this->hydrateAuthorPosts($authorsPostsData);

        return $this->sortByPostCount($authorsPosts);
    }

    /**
     * @return Post[][]
     */
    private function createPostsByAuthor(): array
    {
        $authorPostsData = [];

        foreach ($this->postsProvider->fetchAll() as $post) {
            // skip example post
            if ($post->getId() === 150) {
                continue;
            }

            $authorPostsData[$post->getAuthorId()][] = $post;
        }

        return $authorPostsData;
    }

    /**
     * @param mixed[] $authorsPostsData
     * @return AuthorPosts[]
     */
    private function hydrateAuthorPosts(array $authorsPostsData): array
    {
        $authorsPosts = [];

        foreach ($authorsPostsData as $authorId => $posts) {
            $author = $this->authorsProvider->get($authorId);
            $authorPhoto = $author->getPhoto();
            $postCount = count($posts);
            $postsWordCount = $this->countPostsWords($posts);

            $authorsPosts[] = new AuthorPosts($author->getName(), $authorPhoto, $postCount, $postsWordCount);
        }

        return $authorsPosts;
    }

    /**
     * @param AuthorPosts[] $postsByAuthors
     * @return AuthorPosts[]
     */
    private function sortByPostCount(array $postsByAuthors): array
    {
        usort(
            $postsByAuthors,
            fn (AuthorPosts $firstAuthorPosts, AuthorPosts $secondAuthorPosts) => $secondAuthorPosts->getPostCount() <=> $firstAuthorPosts->getPostCount()
        );

        return $postsByAuthors;
    }

    /**
     * @param Post[] $posts
     */
    private function countPostsWords(array $posts): int
    {
        $postsWordCount = 0;
        foreach ($posts as $post) {
            $postsWordCount += $post->getWordCount();
        }

        return $postsWordCount;
    }
}
