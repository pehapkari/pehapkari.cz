<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FeedController extends AbstractController
{
    /**
     * @Route(path="/admin/feed/xml-feed-generate", name="xml_feed_generate")
     */
    public function xmlFeedGenerate(): Response
    {
        // click action

        return $this->render('feed/xmlFeedGenerate.twig');
    }
}
