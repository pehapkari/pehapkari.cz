<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Controller;

use OpenRealEstate\PriceMap\Repository\AreaPriceRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class PriceMapController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var AreaPriceRepository
     */
    private $areaPriceRepository;

    /**
     * @var Xls
     */
    private $xls;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        FormFactoryInterface $formFactory,
        EngineInterface $templateEngine,
        AreaPriceRepository $areaPriceRepository,
        Xls $xls,
        FlashBagInterface $flashBag
    ) {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
        $this->areaPriceRepository = $areaPriceRepository;
        $this->xls = $xls;
        $this->flashBag = $flashBag;
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     *
     * @Route(path="/upload-xls-price-list", name="upload-xls-price-list", methods={"GET", "POST"})
     */
    public function uploadXlsPriceList(Request $request): Response
    {
        $form = $this->createUploadForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->getData()['file'];

            if (in_array($uploadedFile->getClientOriginalExtension(), ['xls', 'xlsx'], true)) {
                $spreadsheet = $this->xls->load($uploadedFile->getRealPath());
                $this->areaPriceRepository->importWorksheet($spreadsheet->getActiveSheet());

                $this->flashBag->add('XLS byl úspěšně importován.', 'success');
                // @todo redirect
            }

            $form->addError(new FormError(sprintf(
                'Only XLS/XLSX formats are supported. "%s" provided',
                $uploadedFile->getClientOriginalExtension()
            )));
        }

        return $this->templateEngine->renderResponse('real-estate/upload-xls-price-list.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function createUploadForm(): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder();
        $formBuilder->add('file', FileType::class, [
            'label' => 'XLS soubor s cenami',
        ]);
        $formBuilder->add('submit', SubmitType::class);

        return $formBuilder->getForm();
    }
}
