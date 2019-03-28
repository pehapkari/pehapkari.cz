<?php declare(strict_types=1);

namespace OpenTraining\Statie\Controller;

use OpenTraining\Exception\ShouldNotHappenException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Renderable\File\PostFile;

final class StatiePostController extends AbstractController
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var mixed[]
     */
    private $authors = [];

    /**
     * @param mixed[] $authors
     */
    public function __construct(Generator $generator, array $authors)
    {
        $this->generator = $generator;
        $this->authors = $authors;
    }

    /**
     * @Route(path="/blog/", name="blog")
     */
    public function blog(): Response
    {
        $abstractGeneratorFiles = $this->generator->run();
        $values = $abstractGeneratorFiles + ['authors' => $this->authors];

        return $this->render('default/blog.html.twig', $values);
    }

    /**
     * @Route(path="/blog/{postSlug}", name="post", requirements={"postSlug"=".+"})
     */
    public function post(string $postSlug): Response
    {
        $matchedPost = $this->matchPostSlug($postSlug);
        if ($matchedPost === null) {
            throw new ShouldNotHappenException();
        }

        return $this->render('default/post.html.twig', [
            'post' => $matchedPost,
            'authors' => $this->authors
        ]);
    }

    private function matchPostSlug(string $postSlug): ?PostFile
    {
        $posts = $this->generator->run()['posts'] ?? [];

        /** @var PostFile $post */
        foreach ($posts as $post) {
            if ($post->getRelativeUrl()  . '/' !== 'blog/' . $postSlug) {
                continue;
            }

            return $post;
        }

        return null;
    }
}
