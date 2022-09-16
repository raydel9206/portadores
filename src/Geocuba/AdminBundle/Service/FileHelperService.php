<?php

namespace Geocuba\AdminBundle\Service;

use Geocuba\Utils\{Constants, Functions};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\{PageSetup, SheetView};
use PhpOffice\PhpWord\{PhpWord};
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\{Response, ResponseHeaderBag, StreamedResponse};

/**
 * Class SpreadsheetService
 * @package Geocuba\AdminBundle\Service
 */
class FileHelperService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SpreadsheetService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @param bool $page_break
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function createSpreadsheet($title, $subtitle, $page_break = true)
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_LETTER)
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setVerticalCentered(false);

        $style = $spreadsheet->getDefaultStyle();
        $style->getFont()->setSize(9);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet
            ->setTitle($subtitle)
//            ->setShowGridlines(false)
            ->setShowRowColHeaders(true);

        $sheet_view = $sheet->getSheetView();
        $sheet_view
            ->setZoomScale(170)
            ->setView($page_break ? SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW : SheetView::SHEETVIEW_NORMAL);

        $sheet->getHeaderFooter()
            ->setOddHeader('&C&",Bold"&14' . $subtitle)
            ->setOddFooter('&L&",Bold"' . $this->container->getParameter('app_name') . '&C&",Bold"&P/&N&R&",Bold"' . (new \DateTime())->format('d/m/Y h:i A'));

        $spreadsheet->getProperties()
            ->setCompany($this->container->getParameter('app_owner'))
            ->setLastModifiedBy($this->container->get('app.service.security')->getUsername())
            ->setCreated(time())
            ->setTitle($title)
            ->setCreator($this->container->getParameter('app_name'));

        setlocale(LC_TIME, "es_ES", 'Spanish_Spain', 'Spanish');

        return $spreadsheet;
    }

    /**
     * @param $title
     * @return PhpWord
     */
    public function createPhpWord($title)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord(); // TODO: settings
        $phpWord->addFontStyle('header', ['bold' => true, 'size' => 16]);

//        $phpWord->getSettings()->set

        // The current version of PHPWord does not include header with PDFWriter (https://github.com/PHPOffice/PHPWord/pull/1157)
//        $phpWord->addSection()->addHeader()->addPreserveText($title, ['bold' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

//        $header->addImage($this->container->getParameter('kernel.project_dir') . '/web/assets/img/PNG/radiocuba.png', [
//            'width' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(1.25),
//            'height' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(0.48),
//            'scale' => 71,
//            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
//        ]);

        return $phpWord;
    }

    /**
     * @param PhpWord|Spreadsheet|resource|string $object
     * @param string $title
     * @param integer $format
     * @return StreamedResponse
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function stream($object, $title, $format)
    {
        switch ($format) {
            case Constants::FORMATO_PDF:
//                \PhpOffice\PhpWord\Settings::setPdfRenderer(
//                    \PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF,
//                    realpath($this->container->getParameter('kernel.root_dir') . '/../vendor/mpdf/mpdf')
//                );
                \PhpOffice\PhpWord\Settings::setPdfRenderer(
                    \PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF,
                    realpath($this->container->getParameter('kernel.project_dir') . '/vendor/dompdf/dompdf')
                );

                $writer = $object instanceof \PhpOffice\PhpWord\PhpWord ? new \PhpOffice\PhpWord\Writer\PDF($object) : new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf($object);
                $file_extension = 'pdf';
                $content_type = 'application/pdf';

                break;
            case Constants::FORMATO_WORD:
                $writer = new \PhpOffice\PhpWord\Writer\Word2007($object);
                $file_extension = 'docx';
                $content_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

                break;
            case Constants::FORMATO_EXCEL:
                $writer = (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($object))->setPreCalculateFormulas(false);
                $file_extension = 'xlsx';
                $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

                break;
            case Constants::FORMATO_PNG:
                $writer = null;
                $file_extension = 'png';
                $content_type = 'image/png';

                break;
            default:
                return null;
        }

        $response = new StreamedResponse(function () use ($writer, $object) {
            if (is_null($writer)) {
                $file_handler = fopen('php://output', 'wb+');
                fwrite($file_handler, stream_get_contents($object));
                fclose($file_handler);
            } else {
                $writer->save('php://output');
            }
        }, Response::HTTP_OK, ['Content-Type' => $content_type, 'Pragma' => 'public', 'Cache-Control' => 'max-age=0']);

        $disposition = $response->headers->makeDisposition($format === Constants::FORMATO_PNG ? ResponseHeaderBag::DISPOSITION_INLINE : ResponseHeaderBag::DISPOSITION_ATTACHMENT, sprintf('%s - %s.' . $file_extension, Functions::toASCII($title), (new \DateTime())->format('Ymd_hi_A')));
        $response->headers->set('Content-Disposition', $disposition);
//
        return $response;
    }
}