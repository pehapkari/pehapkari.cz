<?php

declare(strict_types=1);

namespace Pehapkari\Blog;

use Pehapkari\Blog\DataProvider\AuthorsProvider;
use Pehapkari\Blog\DataProvider\PostsProvider;
use Pehapkari\Blog\ValueObject\AuthorPosts;
use Pehapkari\Exception\ShouldNotHappenException;
use Symplify\Statie\Renderable\File\PostFile;

final class PostStatsFactory
{
    private PostsProvider $postsProvider;

    private AuthorsProvider $authorsProvider;

    public function __construct(PostsProvider $postsProvider, AuthorsProvider $authorsProvider)
    {
        $this->postsProvider = $postsProvider;
        $this->authorsProvider = $authorsProvider;
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
     * @return array<int, PostFile[]>
     */
    private function createPostsByAuthor(): array
    {
        $authorPostsData = [];
        foreach ($this->postsProvider->provide() as $post) {
            // skip example post
            if ($post->getId() === 150) {
                continue;
            }

            $authorId = (int) $post['author'];
            $authorPostsData[$authorId][] = $post;
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
            $author = $this->authorsProvider->provideById($authorId);
            if ($author === null) {
                throw new ShouldNotHappenException();
            }

            $authorPhoto = $author['photo'] ?? null;
            $postCount = count($posts);
            $postsWordCount = $this->countPostsWords($posts);

            $authorsPosts[] = new AuthorPosts($author['name'], $authorPhoto, $postCount, $postsWordCount);
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
     * @param PostFile[] $posts
     */
    private function countPostsWords(array $posts): int
    {
        $postsWordCount = 0;
        foreach ($posts as $post) {
            $postsWordCount += str_word_count($post->getRawContent());
        }

        return $postsWordCount;
    }
}
