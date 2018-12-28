<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Controller;

use OpenRealEstate\PriceMap\Entity\PriceMap;
use OpenRealEstate\PriceMap\Form\UploadXlsFormType;
use OpenRealEstate\PriceMap\Repository\PriceMapRepository;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PriceMapController extends AbstractController
{
    /**
     * @var PriceMapRepository
     */
    private $priceMapRepository;

    /**
     * @var Xls
     */
    private $xls;

    public function __construct(PriceMapRepository $priceMapRepository, Xls $xls)
    {
        $this->priceMapRepository = $priceMapRepository;
        $this->xls = $xls;
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     *
     * @Route(path="/admin/price-map/upload-xls", name="price_map_upload_xls", methods={"GET", "POST"})
     */
    public function uploadXls(Request $request): Response
    {
        $form = $this->createForm(UploadXlsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->getData()['file'];

            if (in_array($uploadedFile->getClientOriginalExtension(), ['xls', 'xlsx'], true)) {
                $spreadsheet = $this->xls->load($uploadedFile->getRealPath());
                $this->priceMapRepository->importWorksheet($spreadsheet->getActiveSheet());

                $this->addFlash('XLS byl úspěšně importován.', 'success');

                $this->redirectToRoute('easyadmin', [
                    'entity' => PriceMap::class,
                    'action' => 'list',
                ]);
            }

            $form->addError(new FormError(sprintf(
                'Nahrajte XLS/XLSX soubor, místo "%s"',
                $uploadedFile->getClientOriginalExtension()
            )));
        }

        return $this->render('price_map/upload_xls.twig', [
            'form' => $form->createView(),
        ]);
    }
}
