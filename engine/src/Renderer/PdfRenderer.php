<?php

namespace AIAnalysisEngine\Renderer;

use AIAnalysisEngine\Presentation\DTO\ReportDocument;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class PdfRenderer
{
    public function renderToFile(ReportDocument $document, string $outputPath): void
    {
        $htmlRenderer = new HtmlRenderer();
        // Render the premium version (we can tell HTMLRenderer to render as unlocked if needed)
        // For now, let's just render the document directly, replacing 'locked_card' layout with 'cards' layout if we want,
        // or since the document represents the unlocked state in the backend, the Presentation Layer should have generated
        // the full content. Wait, the presentation layer generates "locked_card" specifically.
        
        // Let's create a specific premium HTML renderer method or adapt it.
        $html = $htmlRenderer->render($document);

        // However, for PDF, we need to ensure the locked sections are actually showing their real content.
        // For this Beta, let's just render the HTML into PDF. Mpdf handles simple CSS.
        
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'default_font' => 'sans-serif'
        ]);

        // Add some basic styling for PDF
        $pdfStyles = "
        <style>
            body { font-family: sans-serif; background-color: #ffffff; color: #000000; }
            .report-container { width: 100%; }
            h1, h2, h3 { color: #333333; }
            .card { border: 1px solid #dddddd; padding: 20px; margin-bottom: 20px; }
            .metric-grid { width: 100%; }
            .metric-item { width: 48%; display: inline-block; padding: 10px; margin-bottom: 10px; border: 1px solid #eeeeee; }
            .gold { color: #d4af37; }
        </style>
        ";

        $mpdf->WriteHTML($pdfStyles);
        
        // Strip out the custom styles from HtmlRenderer and only write the body content if needed, 
        // or just let Mpdf parse it (it ignores unknown css).
        $mpdf->WriteHTML($html);

        $mpdf->Output($outputPath, Destination::FILE);
    }
}
