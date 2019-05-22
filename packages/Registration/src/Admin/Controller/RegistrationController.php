<?php declare(strict_types=1);

namespace Pehapkari\Registration\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Invoicing\Invoicer;
use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Certificate\CertificateGenerator;
use Pehapkari\Zip\Zip;
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

    /**
     * @var Invoicer
     */
    private $invoicer;

    public function __construct(
        CertificateGenerator $certificateGenerator,
        Zip $zip,
        TrainingRegistrationRepository $trainingRegistrationRepository,
        Invoicer $invoicer
    ) {
        $this->certificateGenerator = $certificateGenerator;
        $this->zip = $zip;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->invoicer = $invoicer;
    }

    /**
     * @param int[] $ids
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

    /**
     * @param int[] $ids
     */
    public function sendInvoicesBatchAction(array $ids): void
    {
        $registrations = $this->trainingRegistrationRepository->findWithoutInvoicesByIds($ids);

        foreach ($registrations as $registration) {
            $this->invoicer->sendInvoiceForRegistration($registration);

            $this->addFlash(
                'success',
                sprintf(
                    'Faktura pro %s %s byla vytvořena a poslána',
                    $registration->getTrainingName(),
                    $registration->getName()
                )
            );
        }
    }
}
