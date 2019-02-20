<?php declare(strict_types=1);

namespace OpenTraining\Statie\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class StatiePostController extends AbstractController
{
    // @todo redirect post url to generated markdown to twig content from
    // _source/posts ...

    /**
     * @Route(path="/blog/", name="blog")
     */
    public function blog()
    {

    }

    /**
     * @Route(path="/blog/{post-slug}", name="post")
     */
    public function post(string $postSlug)
    {
        dump($postSlug);
    }
}
