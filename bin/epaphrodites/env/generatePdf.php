<?php

namespace Epaphrodites\epaphrodites\env;

class generatePdf
{

    /**
     * @param null|string $content
     * @param null|string $fileName
     * @return never
    */
    public function generate(?string $content = NULL, ?string $fileName = NULL)
    {

        $fileName = $fileName . '.pdf';

        $generatePdf = new \Mpdf\Mpdf;

        $generatePdf->AddPage();

        $generatePdf->WriteHTML($content);

        $pdfContent = $generatePdf->Output('', 'S');

        header('Content-Type: application/pdf');

        header("Content-Disposition: inline; filename='$fileName'");

        echo $pdfContent;

        exit;
    }
}
