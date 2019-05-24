<?php declare(strict_types=1);

namespace Pehapkari\Training\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Pehapkari\Training\PromoImages\PromoImagesGenerator;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Pehapkari\Zip\Zip;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \Pehapkari\Training\Entity\TrainingTerm
 */
final class AdminTrainingTermController extends EasyAdminController
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;
    /**
     * @var Zip
     */
    private $zip;
    /**
     * @var PromoImagesGenerator
     */
    private $promoImagesGenerator;

    public function __construct(TrainingTermRepository $trainingTermRepository, Zip $zip, PromoImagesGenerator $promoImagesGenerator)
    {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->zip = $zip;
        $this->promoImagesGenerator = $promoImagesGenerator;
    }

    /**
     * @param int[] $ids
     */
    public function generatePromoImagesBatchAction(array $ids): Response
    {
        $trainingTerms = $this->trainingTermRepository->findByIds($ids);

        $promoImagePaths = [];
        foreach ($trainingTerms as $trainingTerm) {
            $promoImagePaths[] = $this->promoImagesGenerator->generateForTrainingTerm($trainingTerm);
        }

        dump($promoImagePaths);
        die;

        $zipFileName = sprintf('promo-images-%s.zip', Strings::webalize(new DateTime()));
        $zipFile = $this->zip->saveZipFileWithFiles($zipFileName, $promoImagePaths);

        return $this->file($zipFile);
    }
}
