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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct(
        FormFactoryInterface $formFactory,
        EngineInterface $templateEngine,
        AreaPriceRepository $areaPriceRepository,
        Xls $xls
    ) {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
        $this->areaPriceRepository = $areaPriceRepository;
        $this->xls = $xls;
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     *
     * @Route(path="/upload-xls-price-list", name="upload-xls-price-list", methods={"GET", "POST"})
     */
    public function uploadXlsPriceList(Request $request): Response
    {
        $formBuilder = $this->formFactory->createBuilder();
        $formBuilder->add('file', FileType::class, [
            'label' => 'XLS soubor s cenami',
        ]);
        $formBuilder->add('submit', SubmitType::class);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // See if posted
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->getData()['file'];

            if (! in_array($uploadedFile->getClientOriginalExtension(), ['xls', 'xlsx'], true)) {
                $form->get('file')->addError(new FormError('Only XLS/XLSX formats are supported.'));
            } else {
                $spreadsheet = $this->xls->load($uploadedFile->getRealPath());
                $this->areaPriceRepository->importWorksheet($spreadsheet->getActiveSheet());

                dump('tadá!');
                die;
            }
        }

        return $this->templateEngine->renderResponse('real-estate/upload-xls-price-list.twig', [
            'form' => $form->createView(),
        ]);
    }
}
