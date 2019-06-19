<?php declare(strict_types=1);

namespace Pehapkari\Training\Certificate;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Pehapkari\Pdf\PdfFactory;
use Pehapkari\Registration\Entity\TrainingRegistration;
use setasign\Fpdi\Fpdi;

final class CertificateGenerator
{
    /**
     * @var string
     */
    private $certificateOutputDirectory;

    /**
     * @var PdfFactory
     */
    private $pdfFactory;

    public function __construct(string $certificateOutputDirectory, PdfFactory $pdfFactory)
    {
        $this->certificateOutputDirectory = $certificateOutputDirectory;
        $this->pdfFactory = $pdfFactory;
    }

    /**
     * @return string File path to temp certificate
     */
    public function generateForTrainingTermRegistration(TrainingRegistration $trainingRegistration): string
    {
        $training = $trainingRegistration->getTraining();

        $trainingName = $training->getNameForCertificate();

        $date = $trainingRegistration->getTrainingTermDate()->format('j. n. Y');
        $participantName = (string) $trainingRegistration->getName();

        // @todo add trainer as well!

        return $this->generateForTrainingNameDateAndParticipantName($trainingName, $date, $participantName);
    }

    private function generateForTrainingNameDateAndParticipantName(
        string $trainingName,
        string $date,
        string $userName
    ): string {
        $pdf = $this->pdfFactory->createHorizontalWithTemplate(
            __DIR__ . '/../../../../public/assets/pdf/certificate.pdf'
        );

        $tppl = $pdf->importPage(1);
        $pdf->useTemplate($tppl, 25, 0);

        $this->setBlackColor($pdf);

        $width = (int) $pdf->GetPageWidth();

        $this->addTrainingName($trainingName, $pdf, $width);
        $this->addDate($date, $pdf, $width);
        $this->addVisitorName($userName, $pdf, $width);

        $destination = $this->createDestination($trainingName, $userName);
        // ensure directory exists
        FileSystem::createDir(dirname($destination));

        $pdf->Output('F', $destination);

        return $destination;
    }

    private function setBlackColor(Fpdi $fpdi): void
    {
        $fpdi->SetTextColor(0, 0, 0);
    }

    private function addTrainingName(string $trainingName, Fpdi $fpdi, int $width): void
    {
        $trainingName = $this->encode($trainingName);
        $fontSize = 25;

        $fpdi->SetFont('DejaVuSans', '', $fontSize);

        $fpdi->SetXY(0, 333);

        // see http://www.fpdf.org/en/doc/multicell.htm
        $lineHeight = 30;
        $fpdi->MultiCell($width, $lineHeight, $trainingName, 0, 'C');
    }

    private function addDate(string $date, Fpdi $fpdi, int $width): void
    {
        $date = $this->encode($date);
        $fpdi->SetFont('Georgia', '', 13);

        $fpdi->SetXY(0, 295);
        $fpdi->MultiCell($width, 13, $date, 0, 'C');
    }

    private function addVisitorName(string $name, Fpdi $fpdi, int $width): void
    {
        $name = $this->encode($name);
        $fpdi->SetFont('Georgia', '', 32);

        $fpdi->SetXY(0, 240);
        $fpdi->MultiCell($width, 32, $name, 0, 'C');
    }

    private function createDestination(string $trainingName, string $participantName): string
    {
        return $this->certificateOutputDirectory . '/' .
            sprintf('%s-%s.pdf', Strings::webalize($trainingName), Strings::webalize($participantName));
    }

    private function encode(string $string): string
    {
        return (string) iconv('UTF-8', 'windows-1250', $string);
    }
}
