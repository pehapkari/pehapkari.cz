<?php declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Nette\Utils\Strings;
use Pehapkari\Training\Certificate\CertificateGenerator;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Zip\Zip;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CertificateController extends AbstractController
{
    /**
     * @var CertificateGenerator
     */
    private $certificateGenerator;

    /**
     * @var Zip
     */
    private $zip;

    public function __construct(CertificateGenerator $certificateGenerator, Zip $zip)
    {
        $this->certificateGenerator = $certificateGenerator;
        $this->zip = $zip;
    }

    /**
     * @Route(path="/admin/download-certificates/{id}", name="download_certificates")
     * @see https://symfony.com/blog/new-in-symfony-3-2-file-controller-helper
     */
    public function downloadCertificates(TrainingTerm $trainingTerm): Response
    {
        $certificateFilePaths = [];
        foreach ($trainingTerm->getRegistrations() as $trainingTermRegistration) {
            $certificateFilePaths[] = $this->certificateGenerator->generateForTrainingTermRegistration(
                $trainingTermRegistration
            );
        }

        $zipFileName = sprintf('certifikaty-%s.zip', Strings::webalize($trainingTerm->getTrainingName()));
        $zipFile = $this->zip->saveZipFileWithFiles($zipFileName, $certificateFilePaths);

        return $this->file($zipFile);
    }
}
