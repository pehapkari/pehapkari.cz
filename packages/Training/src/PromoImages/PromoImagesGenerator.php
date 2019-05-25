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

    public function __construct(string $promoImageAssetsDirectory, string $promoImageOutputDirectory)
    {
        // required for Fpdi
        define('FPDF_FONTPATH', $promoImageAssetsDirectory . '/fonts');

        $this->promoImageAssetsDirectory = $promoImageAssetsDirectory;
        $this->promoImageOutputDirectory = $promoImageOutputDirectory;
    }

    /**
     * @return string File path to temp promoImages
     */
    public function generateForTrainingTerm(TrainingTerm $trainingTerm): string
    {
        $trainingName = $trainingTerm->getTrainingName();
        $date = $trainingTerm->getStartDateTime()->format('j. n. Y');

        return $this->generateForTrainingNameDateAndParticipantName($trainingName, $date, $trainingTerm);
    }

    private function generateForTrainingNameDateAndParticipantName(
        string $trainingName,
        string $date,
        TrainingTerm $trainingTerm
    ): string {
        $pdf = $this->createLandscapePdfWithFonts();

        $pageId = $pdf->importPage(1);
        $pdf->useTemplate($pageId, 25, 0);

        $width = (int) $pdf->GetPageWidth();

        $this->addTrainingName($trainingName, $pdf);
        $this->addDate($date, $pdf, $width);

        // @done
        $this->addTrainingImage($trainingTerm, $pdf);
        $this->addTrainerImage($trainingTerm, $pdf);

        $destination = $this->createFileDestination($trainingName, $trainingTerm);

        // ensure directory exists
        FileSystem::createDir(dirname($destination));

        // F = filesystem
        $pdf->Output('F', $destination);

        return $destination;
    }

    private function createLandscapePdfWithFonts(): Fpdi
    {
        $pdf = new Fpdi('landscape', 'pt');
        $pdf->AddPage('landscape');

        // encode font here - http://www.fpdf.org/makefont - cp-1250
        $pdf->AddFont('OpenSans', '', 'OpenSans-Regular.php');
        $pdf->SetFont('OpenSans', '', 14);

        $pdf->setSourceFile($this->promoImageAssetsDirectory . '/promo_image.pdf');

        return $pdf;
    }

    private function addTrainingName(string $trainingName, Fpdi $fpdi): void
    {
        $trainingName = $this->encode($trainingName);

        // resize for long lecture names
        $fontSize = strlen($trainingName) < 45 ? 35 : strlen($trainingName) < 45 ? 24 : 22;
        $fpdi->SetFontSize($fontSize);

        // @todo use BOLD here
        $greenColorRGB = [143, 190, 0];
        $fpdi->SetTextColor(...$greenColorRGB);

        $fpdi->SetXY(260, 90);
        $fpdi->Write(0, $trainingName);
    }

    private function addDate(string $date, Fpdi $fpdi, int $width): void
    {
        $date = $this->encode($date);
        $fpdi->SetTextColor(0, 0, 0);
        $fpdi->SetXY(($width / 2) - ($fpdi->GetStringWidth($date) / 2), 300);
        $fpdi->Write(0, $date);
    }

    /**
     * @done
     */
    private function addTrainingImage(TrainingTerm $trainingTerm, Fpdi $fpdi): void
    {
        $trainingImage = $trainingTerm->getTrainingImageAbsolutePath();
        $this->ensureFileExists($trainingImage);

        $imageSquareSize = 140;
        $fpdi->Image($trainingImage, 75, 60, $imageSquareSize, $imageSquareSize);
    }

    /**
     * @done
     */
    private function addTrainerImage(TrainingTerm $trainingTerm, Fpdi $fpdi): void
    {
        $trainerImage = $trainingTerm->getTrainerImageAbsolutePath();
        $this->ensureFileExists($trainerImage);

        $imageSquareSize = 160;

        $fpdi->Image($trainerImage, 440, 230, $imageSquareSize, $imageSquareSize);
    }

    private function createFileDestination(string $trainingName, TrainingTerm $trainingTerm): string
    {
        return $this->promoImageOutputDirectory . '/' .
            sprintf(
                'promo-image-%s-%s.pdf',
                Strings::webalize($trainingName),
                Strings::webalize($trainingTerm->getStartDateTime()->format('Y-m-d'))
            );
    }

    private function encode(string $string): string
    {
        return (string) iconv('UTF-8', 'windows-1250', $string);
    }

    private function ensureFileExists(string $trainingImage): void
    {
        if (file_exists($trainingImage)) {
            return;
        }

        throw new ShouldNotHappenException(sprintf('File "%s" was not found.', $trainingImage));
    }
}
