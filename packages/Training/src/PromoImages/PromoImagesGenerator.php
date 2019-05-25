<?php declare(strict_types=1);

namespace Pehapkari\Training\PromoImages;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Pdf\RgbColor;
use setasign\Fpdi\Fpdi;

final class PromoImagesGenerator
{
    /**
     * @var string
     */
    private $promoImageOutputDirectory;

    public function __construct(string $promoImageAssetsDirectory, string $promoImageOutputDirectory)
    {
        // required for Fpdi
        define('FPDF_FONTPATH', $promoImageAssetsDirectory . '/fonts');

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

        $this->addTrainingName($trainingName, $pdf);
        $this->addDate($date, $pdf);

        // @done
        $this->addTrainingImage($trainingTerm, $pdf);
        $this->addTrainerImage($trainingTerm, $pdf);
        $this->addTrainerName($trainingTerm, $pdf);

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

        $this->loadFontsToPdf($pdf);

        $pdf->setSourceFile(__DIR__ . '/../../../../public/assets/promo_image/promo_image.pdf');

        return $pdf;
    }

    private function addTrainingName(string $trainingName, Fpdi $fpdi): void
    {
        $trainingName = $this->encode($trainingName);

        // resize for long lecture names
        $fontSize = strlen($trainingName) < 45 ? 35 : strlen($trainingName) < 45 ? 24 : 22;

        $fpdi->SetTextColor(...RgbColor::GREEN);
        $fpdi->SetFont('OpenSans', 'Bold', $fontSize);

        $fpdi->SetXY(260, 90);
        $fpdi->Write(0, $trainingName);

        // back to black
        $fpdi->SetTextColor(...RgbColor::BLACK);
    }

    private function addDate(string $date, Fpdi $fpdi): void
    {
        $this->writeTextInSizeToLocation($date, 20, 200, 255, $fpdi);
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

    private function addTrainerName(TrainingTerm $trainingTerm, Fpdi $fpdi): void
    {
        $this->writeTextInSizeToLocation('školí ' . $trainingTerm->getTrainerName(), 20, 180, 310, $fpdi);
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

    /**
     * Encode font here to get *.php and *.t versions: http://www.fpdf.org/makefont
     * Use cp-1250
     */
    private function loadFontsToPdf(Fpdi $fpdi): void
    {
        $fpdi->AddFont('OpenSans', '', 'OpenSans-Regular.php');
        $fpdi->AddFont('OpenSans', 'Bold', 'OpenSans-Bold.php');
        $fpdi->SetFont('OpenSans', '', 14);
    }

    private function encode(string $string): string
    {
        return (string) iconv('UTF-8', 'windows-1250', $string);
    }

    private function writeTextInSizeToLocation(string $text, int $fontSize, int $x, int $y, Fpdi $fpdi): void
    {
        $text = $this->encode($text);
        $fpdi->SetFontSize($fontSize);

        $fpdi->SetXY($x, $y);
        $fpdi->Write(0, $text);
    }

    private function ensureFileExists(string $trainingImage): void
    {
        if (file_exists($trainingImage)) {
            return;
        }

        throw new ShouldNotHappenException(sprintf('File "%s" was not found.', $trainingImage));
    }
}
