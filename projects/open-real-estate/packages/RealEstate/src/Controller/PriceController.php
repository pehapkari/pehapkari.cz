<?php declare(strict_types=1);

namespace OpenRealEstate\RealEstate\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PriceController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    public function __construct(FormFactoryInterface $formFactory, EngineInterface $templateEngine)
    {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @Route(path="/upload-xls-price-list", name="upload-xls-price-list", methods={"GET", "POST"})
     */
    public function uploadXlsPriceList(Request $request): Response
    {
        $uploadXlsPriceForm = $this->formFactory->create(FileType::class);

        $uploadXlsPriceForm->handleRequest($request);

        if ($uploadXlsPriceForm->isSubmitted() && $uploadXlsPriceForm->isValid()) {
            $formData = $uploadXlsPriceForm->getData();
            dump($formData);
            die;
        }

        return $this->templateEngine->renderResponse('real-estate/upload-xls-price-list.twig', [
            'form' => $uploadXlsPriceForm->createView()
        ]);
    }

//    public function default(Request $request, Training $training): Response
//    {
//        $form = $this->formFactory->create(TrainingRegistrationFormType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var TrainingRegistration $trainingRegistration */
//            $trainingRegistration = $form->getData();
//            $this->trainingRegistrationRepository->save($trainingRegistration);
//
//            // @todo translate
//            $this->flashBag->add('succes', 'training.registration_successful');
//
//            return new RedirectResponse($this->router->generate(
//                'register-to-training',
//                ['training' => $training->getId()]
//            ));
//        }
//
//        return $this->templatingEngine->renderResponse('registration/default.twig', [
//            'training' => $training,
//            'form' => $form->createView(),
//        ]);
//    }
}
