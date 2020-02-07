<?php

declare(strict_types=1);

namespace Pehapkari\Training\Certificate;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Pdf\PdfFactory;
use Pehapkari\Training\ValueObject\Font;
use Pehapkari\Zip\Zip;
use setasign\Fpdi\Fpdi;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

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

    /**
     * @var Fpdi
     */
    private $fpdi;

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    /**
     * @var Zip
     */
    private $zip;

    public function __construct(
        string $certificateOutputDirectory,
        PdfFactory $pdfFactory,
        PrivatesAccessor $privatesAccessor,
        Zip $zip
    ) {
        $this->certificateOutputDirectory = $certificateOutputDirectory;
        $this->pdfFactory = $pdfFactory;
        $this->privatesAccessor = $privatesAccessor;
        $this->zip = $zip;
    }

    /**
     * @param TrainingRegistration[] $registrations
     */
    public function generateForRegistrationsToZipFile(array $registrations): string
    {
        $certificateFilePaths = [];
        foreach ($registrations as $trainingRegistration) {
            $certificateFilePaths[] = $this->generateForTrainingTermRegistration($trainingRegistration);
        }

        $zipFileName = sprintf('certifikaty-%s.zip', Strings::webalize((string) new DateTime()));

        return $this->zip->saveZipFileWithFiles($zipFileName, $certificateFilePaths);
    }

    /**
     * @return string File path to temp certificate
     */
    public function generateForTrainingTermAndName(TrainingTerm $trainingTerm, string $participantName): string
    {
        $training = $trainingTerm->getTraining();

        $trainingName = $training->getNameForCertificate();

        $date = $trainingTerm->getStartDateTime()->format('j. n. Y');

        return $this->generateForTrainingNameDateAndParticipantName($trainingName, $date, $participantName);
    }

    /**
     * @return string File path to temp certificate
     */
    private function generateForTrainingTermRegistration(TrainingRegistration $trainingRegistration): string
    {
        $training = $trainingRegistration->getTraining();

        $trainingName = $training->getNameForCertificate();

        $date = $trainingRegistration->getTrainingTermDate()->format('j. n. Y');
        $participantName = (string) $trainingRegistration->getName();

        return $this->generateForTrainingNameDateAndParticipantName($trainingName, $date, $participantName);
    }

    private function generateForTrainingNameDateAndParticipantName(
        string $trainingName,
        string $date,
        string $participantName
    ): string {
        $this->fpdi = $this->pdfFactory->createHorizontalWithTemplate(
            __DIR__ . '/../../../../public/assets/pdf/certificate_empty.pdf'
        );

        $tppl = $this->fpdi->importPage(1);
        $this->fpdi->useTemplate($tppl);

        $this->setBlackColor();

        // in the order from the top to the bottom
        $this->addParticipantName($participantName);
        $this->addDate($date);
        $this->addTrainingName($trainingName);

        $destination = $this->createDestination($trainingName, $participantName);
        // ensure directory exists
        FileSystem::createDir(dirname($destination));

        $this->fpdi->Output('F', $destination);

        return $destination;
    }

    private function setBlackColor(): void
    {
        $this->fpdi->SetTextColor(0, 0, 0);
    }

    private function addParticipantName(string $participantName): void
    {
        $this->fpdi->SetFont(Font::BUNDAY_ITALIC, '', 73);
        $this->addTextToCenter($participantName, 209);
    }

    private function addDate(string $date): void
    {
        $this->fpdi->SetFont(Font::BUNDAY_BOLD, '', 21);
        $this->addTextToCenter($date, 363);
    }

    private function addTrainingName(string $trainingName): void
    {
        $trainingName = Strings::upper($trainingName);

        $this->fpdi->SetFont(Font::BUNDAY_BOLD, '', 35);
        $this->addTextToCenter($trainingName, 395);
    }

    private function createDestination(string $trainingName, string $participantName): string
    {
        return $this->certificateOutputDirectory . '/' .
            sprintf('%s-%s.pdf', Strings::webalize($trainingName), Strings::webalize($participantName));
    }

    private function addTextToCenter(string $text, int $y): void
    {
        $text = $this->encode($text);

        // set line-height to current font size
        $fontSize = $this->privatesAccessor->getPrivateProperty($this->fpdi, 'FontSize');
        $lineHeight = $fontSize + 5;

        // see http://www.fpdf.org/en/doc/multicell.htm
        $this->fpdi->SetXY(0, $y);
        $this->fpdi->MultiCell($this->fpdi->GetPageWidth(), $lineHeight, $text, 0, 'C');
    }

    private function encode(string $string): string
    {
        return (string) iconv('UTF-8', 'windows-1250', $string);
    }
}
