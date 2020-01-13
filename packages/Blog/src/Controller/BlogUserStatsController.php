<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Controller;

use Pehapkari\Blog\PostStatsFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogUserStatsController extends AbstractController
{
    /**
     * @var PostStatsFactory
     */
    private $postStatsFactory;

    public function __construct(PostStatsFactory $postStatsFactory)
    {
        $this->postStatsFactory = $postStatsFactory;
    }

    /**
     * @Route(path="community-bloggers", name="blog_user_stats")
     */
    public function __invoke(): Response
    {
        return $this->render('blog/blog_user_stats.twig', [
            'authors_posts' => $this->postStatsFactory->create(),
            'post_break_count' => 3,
        ]);
    }
}
