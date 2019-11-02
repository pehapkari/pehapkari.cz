<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Repository\PostRepository;
use Pehapkari\Statie\AuthorsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogPostController extends AbstractController
{
    /**
     * @Route(path="blog/{postSlug}", name="post", requirements={"postSlug"=".+"})
     */
    public function __invoke(
        string $postSlug,
        AuthorsProvider $authorsProvider,
        PostRepository $postRepository
    ): Response {
        $post = $postRepository->findBySlug($postSlug);
        if ($post === null) {
            throw $this->createNotFoundException(sprintf("Post with slug '%s' not found", $postSlug));
        }

        return $this->render('blog/post.twig', [
            'post' => $post,
            'authors' => $authorsProvider->provide(),
            'title' => $post->getConfiguration()['title'] ?? null,
        ]);
    }
}
