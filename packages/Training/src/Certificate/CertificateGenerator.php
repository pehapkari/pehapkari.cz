<?php declare(strict_types=1);

namespace Pehapkari\Training\Certificate;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Pehapkari\Registration\Entity\TrainingRegistration;
use setasign\Fpdi\Fpdi;

final class CertificateGenerator
{
    /**
     * @var string
     */
    private $certificateAssetsDirectory;

    /**
     * @var string
     */
    private $certificateOutputDirectory;

    public function __construct(string $certificateAssetsDirectory, string $certificateOutputDirectory)
    {
//        // required for Fpdi
//        define('FPDF_FONTPATH', $certificateAssetsDirectory . '/fonts');

        $this->certificateAssetsDirectory = $certificateAssetsDirectory;
        $this->certificateOutputDirectory = $certificateOutputDirectory;
    }

    /**
     * @return string File path to temp certificate
     */
    public function generateForTrainingTermRegistration(TrainingRegistration $trainingRegistration): string
    {
        $trainingName = $trainingRegistration->getTrainingName();
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
        $pdf = new Fpdi('landscape', 'pt');
        $pdf->AddPage('landscape');

        $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
        $pdf->AddFont('Georgia', '', 'Georgia.php');

        $pdf->setSourceFile($this->certificateAssetsDirectory . '/certificate.pdf');
        $tppl = $pdf->importPage(1);
        $pdf->useTemplate($tppl, 25, 0);

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

    private function addTrainingName(string $trainingName, Fpdi $fpdi, int $width): void
    {
        $trainingName = $this->encode($trainingName);

        // resize for long lecture names
        $fontSize = strlen($trainingName) < 40 ? 23 : strlen($trainingName) < 45 ? 21 : 18;

        $fpdi->SetFont('DejaVuSans', '', $fontSize);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($trainingName) / 2), 350);
        $fpdi->Write(0, $trainingName);
    }

    private function addDate(string $date, Fpdi $fpdi, int $width): void
    {
        $date = $this->encode($date);
        $fpdi->SetFont('Georgia', '', 13);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($date) / 2), 300);
        $fpdi->Write(0, $date);
    }

    private function addVisitorName(string $name, Fpdi $fpdi, int $width): void
    {
        $name = $this->encode($name);
        $fpdi->SetFont('Georgia', '', 32);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($name) / 2), 260);
        $fpdi->Write(0, $name);
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
