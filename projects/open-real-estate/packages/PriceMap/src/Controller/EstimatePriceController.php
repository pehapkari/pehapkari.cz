<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Controller;

use OpenRealEstate\PriceMap\Form\EstimatePriceFormType;
use OpenRealEstate\PriceMap\Repository\PriceMapRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EstimatePriceController extends AbstractController
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
        $form = $this->createForm(EstimatePriceFormType::class);
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

        return $this->render('estimate_real_estate/default.twig', [
            'form' => $form->createView(),
            'price' => $price,
        ]);
    }
}
