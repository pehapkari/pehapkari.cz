<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use OpenRealEstate\PriceMap\Form\EstimatePriceFormType;
use OpenRealEstate\PriceMap\Repository\PriceMapRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EstimatePriceController extends EasyAdminController
{
    /**
     * @var PriceMapRepository
     */
    private $priceMapRepository;

    public function __construct(PriceMapRepository $priceMapRepository)
    {
        $this->priceMapRepository = $priceMapRepository;
    }

    /**
     * @see https://symfony.com/doc/current/controller/upload_file.html
     *
     * @Route(path="/admin/price-map/estimate-price", name="price_map_estimate_price")
     */
    public function estimatePrice(Request $request): Response
    {
        $form = $this->createForm(EstimatePriceFormType::class, null, [
            'entity' => 'PriceMap', // same as key in the config, not a class
            'view' => 'random',
        ]);
        $form->handleRequest($request);
        $price = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $price = $this->priceMapRepository->findPriceByZipAndType($data['zip'], $data['type']);

            if ($price === null) {
                $form->addError(new FormError('Nenašli jsme cenu pro PSČ "%s"', $data['zip']));
                // redirect self
                $this->redirectToRoute('price_map_estimate_price');
            }

            $price *= $data['area'];
        }

        // needs to be modified because native `EasyAdminExtension` skips it for custom forms
        // @see https://github.com/EasyCorp/EasyAdminBundle/issues/2565#issuecomment-450579629
        $formView = $form->createView();
        $formView = $this->finishView($formView);

        return $this->render('estimate_real_estate/default.twig', [
            'form' => $formView,
            'price' => $price,
        ]);
    }

    private function finishView(FormView $formView): FormView
    {
    }
}
