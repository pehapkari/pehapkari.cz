<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Controller;

use Pehapkari\Blog\DataProvider\AuthorsProvider;
use Pehapkari\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogPostController extends AbstractController
{
    /**
     * @var AuthorsProvider
     */
    private $authorsProvider;

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(AuthorsProvider $authorsProvider, PostRepository $postRepository)
    {
        $this->authorsProvider = $authorsProvider;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="blog/{postSlug}", name="post", requirements={"postSlug"=".+"})
     */
    public function __invoke(string $postSlug): Response
    {
        $post = $this->postRepository->findBySlug($postSlug);
        if ($post === null) {
            throw $this->createNotFoundException(sprintf("Post with slug '%s' not found", $postSlug));
        }

        return $this->render('blog/post.twig', [
            'post' => $post,
            'authors' => $this->authorsProvider->provide(),
            'title' => $post->getConfiguration()['title'] ?? null,
        ]);
    }
}
