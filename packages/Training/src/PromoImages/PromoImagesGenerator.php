<?php declare(strict_types=1);

namespace Pehapkari\Training\PromoImages;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Training\Entity\TrainingTerm;
use setasign\Fpdi\Fpdi;

final class PromoImagesGenerator
{
    /**
     * @var string
     */
    private $promoImageAssetsDirectory;

    /**
     * @var string
     */
    private $promoImageOutputDirectory;

    /**
     * @var string
     */
    private $uploadDestination;

    public function __construct(
        string $promoImageAssetsDirectory,
        string $promoImageOutputDirectory,
        string $uploadDestination
    ) {
        // required for Fpdi
        define('FPDF_FONTPATH', $promoImageAssetsDirectory . '/fonts');

        $this->promoImageAssetsDirectory = $promoImageAssetsDirectory;
        $this->promoImageOutputDirectory = $promoImageOutputDirectory;
        $this->uploadDestination = $uploadDestination;
    }

    /**
     * @return string File path to temp promoImages
     */
    public function generateForTrainingTerm(TrainingTerm $trainingTerm): string
    {
        $trainingName = $trainingTerm->getTrainingName();
        $date = $trainingTerm->getStartDateTime()->format('j. n. Y');
        $participantName = $trainingTerm->getTrainingName();

        return $this->generateForTrainingNameDateAndParticipantName(
            $trainingName,
            $date,
            $participantName,
            $trainingTerm
        );
    }

    private function generateForTrainingNameDateAndParticipantName(
        string $trainingName,
        string $date,
        string $userName,
        TrainingTerm $trainingTerm
    ): string {
        $pdf = new Fpdi('l', 'pt');
        $pdf->AddPage('l');

        // encode font here - http://www.fpdf.org/makefont - cp-1250
        $pdf->AddFont('OpenSans', '', 'OpenSans-Regular.php');
        $pdf->SetFont('OpenSans', '', 14);

        $pdf->setSourceFile($this->promoImageAssetsDirectory . '/promo_image.pdf');
        $tppl = $pdf->importPage(1);
        $pdf->useTemplate($tppl, 25, 0);

        $width = (int) $pdf->GetPageWidth();

        $this->addTrainingName($trainingName, $pdf, $width);
        $this->addDate($date, $pdf, $width);

        // add training image
        $trainingImage = $this->uploadDestination . $trainingTerm->getTraining()->getImage();
        $this->ensureFileExists($trainingImage);
        $pdf->Image($trainingImage, 100, 500);

        // add trainer photo
        $trainerImage = $this->uploadDestination . $trainingTerm->getTrainer()->getImage();
        $this->ensureFileExists($trainerImage);
        $pdf->Image($trainerImage, 40, 150);

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

//        $fpdi->SetFont('OpenSans', '', $fontSize);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($trainingName) / 2), 350);
        $fpdi->Write(0, $trainingName);
    }

    private function addDate(string $date, Fpdi $fpdi, int $width): void
    {
        $date = $this->encode($date);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($date) / 2), 300);
        $fpdi->Write(0, $date);
    }

    private function ensureFileExists(string $trainingImage): void
    {
        if (file_exists($trainingImage)) {
            return;
        }

        throw new ShouldNotHappenException(sprintf('File "%s" was not found.', $trainingImage));
    }

    private function createDestination(string $trainingName, string $participantName): string
    {
        return $this->promoImageOutputDirectory . '/' .
            sprintf('promo-image-%s-%s.pdf', Strings::webalize($trainingName), Strings::webalize($participantName));
    }

    private function encode(string $string): string
    {
        return (string) iconv('UTF-8', 'windows-1250', $string);
    }
}
