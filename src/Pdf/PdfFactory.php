<?php

declare(strict_types=1);

namespace Pehapkari\Pdf;

use setasign\Fpdi\Fpdi;

final class PdfFactory
{
    public function __construct()
    {
        // required for Fpdi
        define('FPDF_FONTPATH', __DIR__ . '/../../public/assets/fonts');
    }

    public function createHorizontal(): Fpdi
    {
        $fpdi = new Fpdi('landscape', 'pt');
        $fpdi->AddPage('landscape');

        $this->loadFontsToPdf($fpdi);

        return $fpdi;
    }

    public function createHorizontalWithTemplate(string $template): Fpdi
    {
        $fpdi = $this->createHorizontal();
        $fpdi->setSourceFile($template);

        return $fpdi;
    }

    /**
     * Encode font here to get *.php and *.t versions: http://www.fpdf.org/makefont
     * Use cp-1250
     */
    private function loadFontsToPdf(Fpdi $fpdi): void
    {
        // certificates
        $fpdi->AddFont('DejaVuSans', '', 'DejaVuSans.php');
        $fpdi->AddFont('Georgia', '', 'Georgia.php');
    }
}
