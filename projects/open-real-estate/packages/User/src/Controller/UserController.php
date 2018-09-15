<?php declare(strict_types=1);

namespace OpenRealEstate\User\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @see https://github.com/EliHood/symfonyormexample/blob/master/src/Controller/UserController.php
 */
final class UserController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(AuthenticationUtils $authenticationUtils, EngineInterface $templateEngine)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->templateEngine->renderResponse('security/login.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }
}
