<?php declare(strict_types=1);

namespace Pehapkari\Training\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Pehapkari\Marketing\MarketingCampaignFactory;
use Pehapkari\Marketing\Repository\MarketingCampaignRepository;
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

    /**
     * @var \Pehapkari\Marketing\Repository\MarketingCampaignRepository
     */
    private $marketingCampaignRepository;

    /**
     * @var MarketingCampaignFactory
     */
    private $marketingCampaignFactory;

    public function __construct(
        Zip $zip,
        PromoImagesGenerator $promoImagesGenerator,
        TrainingTermRepository $trainingTermRepository,
        MarketingCampaignRepository $marketingCampaignRepository,
        MarketingCampaignFactory $marketingCampaignFactory
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->zip = $zip;
        $this->promoImagesGenerator = $promoImagesGenerator;
        $this->marketingCampaignRepository = $marketingCampaignRepository;
        $this->marketingCampaignFactory = $marketingCampaignFactory;
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

        $zipFileName = sprintf('promo-images-%s.zip', Strings::webalize((string) new DateTime()));
        $zipFile = $this->zip->saveZipFileWithFiles($zipFileName, $promoImagePaths);

        return $this->file($zipFile);
    }

    /**
     * @param int[] $ids
     */
    public function generateMarketingCampaignBatchAction(array $ids): void
    {
        $trainingTerms = $this->trainingTermRepository->findByIds($ids);

        foreach ($trainingTerms as $trainingTerm) {
            if ($this->marketingCampaignRepository->hasTrainingTermMarketingCampaign($trainingTerm)) {
                $this->addFlash('warning', sprintf('Kampaň pro termín "%s" už existuje', (string) $trainingTerm));
                continue;
            }

            $marketingCampaign = $this->marketingCampaignFactory->createMarketingCampaign($trainingTerm);

            $this->marketingCampaignRepository->save($marketingCampaign);

            $this->addFlash('success', sprintf('Kampaň pro "%s" byla vytvořena', (string) $trainingTerm));
        }
    }
}
