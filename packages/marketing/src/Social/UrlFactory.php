<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Social;

use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\Routing\RouterInterface;

final class UrlFactory
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createAbsoluteTrainingUrl(TrainingTerm $trainingTerm): string
    {
        return 'https://pehapkari.cz' . $this->router->generate(
            'training_detail',
            ['slug' => $trainingTerm->getTrainingSlug()]
        );
    }
}
