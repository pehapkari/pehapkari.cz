<?php

declare(strict_types=1);

namespace Pehapkari\Wiki\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminWikiController extends EasyAdminController
{
    /**
     * @Route(path="admin/wiki/organize-training-term", name="wiki_organize_training_term")
     */
    public function __invoke(): Response
    {
        return $this->render('wiki/organize_training_term.twig');
    }
}
