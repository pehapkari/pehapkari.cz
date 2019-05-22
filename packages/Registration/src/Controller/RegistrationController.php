<?php declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Certificate\CertificateGenerator;
use Pehapkari\Zip\Zip;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see TrainingRegistration
 */
final class RegistrationController extends EasyAdminController
{
    /**
     * @var CertificateGenerator
     */
    private $certificateGenerator;

    /**
     * @var Zip
     */
    private $zip;
    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(CertificateGenerator $certificateGenerator, Zip $zip, TrainingRegistrationRepository $trainingRegistrationRepository)
    {
        $this->certificateGenerator = $certificateGenerator;
        $this->zip = $zip;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
    }

    /**
     * @Route(path="/admin/download-certificates/{id}", name="download_certificates")
     * @see https://symfony.com/blog/new-in-symfony-3-2-file-controller-helper
     */
    public function certificateBatchAction(array $ids): Response
    {
        $registrations = $this->trainingRegistrationRepository->findByIds($ids);

        $certificateFilePaths = [];
        foreach ($registrations as $trainingRegistration) {
            $certificateFilePaths[] = $this->certificateGenerator->generateForTrainingTermRegistration(
                $trainingRegistration
            );
        }

        $zipFileName = sprintf('certifikaty-%s.zip', Strings::webalize(new DateTime()));
        $zipFile = $this->zip->saveZipFileWithFiles($zipFileName, $certificateFilePaths);

        return $this->file($zipFile);
    }

}
