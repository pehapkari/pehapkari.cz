<?php declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    public function __construct(AuthenticationUtils $authenticationUtils, EngineInterface $templatingEngine)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @Route("/login/", name="security_login")
     */
    public function loginAction(): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user

        return $this->templatingEngine->renderResponse('security/login.html.twig', [
            // last username entered by the user
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $error,
        ]);
    }
}
