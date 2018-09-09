<?php declare(strict_types=1);

namespace OpenRealEstate\RealEstate\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

final class PriceController
{
    /**
     * @Route(path="/upload-xls-price-list", name="upload-xls-price-list", methods={"GET", "POST"})
     */
    public function uploadXlsPriceList()
    {
        dump('EEE');
        die;
    }
}
