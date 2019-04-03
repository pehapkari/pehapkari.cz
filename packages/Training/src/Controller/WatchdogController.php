<?php declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\Watchdog;
use Pehapkari\Training\Form\WatchdogFormType;
use Pehapkari\Training\Repository\WatchdogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @see Watchdog
 */
final class WatchdogController extends AbstractController
{
    /**
     * @var WatchdogRepository
     */
    private $watchdogRepository;

    public function __construct(WatchdogRepository $watchdogRepository)
    {
        $this->watchdogRepository = $watchdogRepository;
    }

    /**
     * @Route(path="/chci-na-dalsi-termin/{slug}", name="watchdog")
     */
    public function watchdog(Training $training, Request $request): Response
    {
        $watchdog = new Watchdog();
        $watchdog->setTraining($training);

        // @see https://stackoverflow.com/a/42975477/1348344
        $form = $this->createForm(WatchdogFormType::class, $watchdog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Watchdog $watchdog */
            $watchdog = $form->getData();
            $this->watchdogRepository->save($watchdog);

            $this->addFlash('success', 'Díky, dáme ti včas vědět!');

            return $this->redirectToRoute('watchdog_thank_you');
        }

        return $this->render('watchdog/watchdog.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/diky-za-feedback/", name="thank_you_for_freedback")
     */
    public function thankYouForFeedback(): Response
    {
        return $this->render('feedback/thank_you_for_freedback.twig');
    }

    /**
     * @Route(path="/pohlidame-ti-to/", name="watchdog_thank_you")
     */
    public function watchdogThankYou(): Response
    {
        return $this->render('watchdog/watchdog_thank_you.twig');
    }
}
