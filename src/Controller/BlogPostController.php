<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statie\AuthorsProvider;
use Pehapkari\Statie\PostsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\Statie\Renderable\File\PostFile;

final class BlogPostController extends AbstractController
{
    /**
     * @var PostsProvider
     */
    private $postsProvider;

    public function __construct(PostsProvider $postsProvider)
    {
        $this->postsProvider = $postsProvider;
    }

    /**
     * @Route(path="blog/{postSlug}", name="post", requirements={"postSlug"=".+"})
     */
    public function __invoke(string $postSlug, AuthorsProvider $authorsProvider): Response
    {
        $matchedPost = $this->matchPostSlug($postSlug);
        if ($matchedPost === null) {
            throw $this->createNotFoundException(sprintf("Post with slug '%s' not found", $postSlug));
        }

        return $this->render('blog/post.twig', [
            'post' => $matchedPost,
            'authors' => $authorsProvider->provide(),
            'title' => $matchedPost->getConfiguration()['title'] ?? null,
        ]);
    }

    private function matchPostSlug(string $postSlug): ?PostFile
    {
        $posts = $this->postsProvider->provide();

        /** @var PostFile $post */
        foreach ($posts as $post) {
            if ($post->getRelativeUrl() . '/' === 'blog/' . $postSlug || $post->getRelativeUrl() === 'blog/' . $postSlug) {
                return $post;
            }
        }

        return null;
    }
}
