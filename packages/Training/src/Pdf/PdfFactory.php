<?php

declare(strict_types=1);

namespace Pehapkari\Training\Pdf;

use Pehapkari\Training\ValueObject\Font;
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
     * Encode font here to get *.php and *.t versions: http://www.fpdf.org/makefont - ***pick cp-1250***!!!!! cp-1252 is picked by default - this caused me hour of shit
     *
     * If it says: "Error: OpenType fonts based on PostScript outlines are not supported"
     * use try this: https://stackoverflow.com/a/2875916/1348344
     * to create True Type font a
     */
    private function loadFontsToPdf(Fpdi $fpdi): void
    {
        foreach (Font::ALL_FONTS as $font) {
            $fpdi->AddFont($font, '', $font . '.php');
        }
    }
}
