<?php

declare(strict_types=1);

namespace Pehapkari\Repository;

use Pehapkari\Blog\DataProvider\PostsProvider;
use Symplify\Statie\Renderable\File\PostFile;

final class PostRepository
{
    /**
     * @var PostsProvider
     */
    private $postsProvider;

    public function __construct(PostsProvider $postsProvider)
    {
        $this->postsProvider = $postsProvider;
    }

    public function findBySlug(string $slug): ?PostFile
    {
        $posts = $this->postsProvider->provide();

        /** @var PostFile $post */
        foreach ($posts as $post) {
            if ($post->getRelativeUrl() . '/' === 'blog/' . $slug || $post->getRelativeUrl() === 'blog/' . $slug) {
                return $post;
            }
        }

        return null;
    }
}
