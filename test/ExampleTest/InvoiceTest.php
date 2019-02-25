<?php

declare(strict_types=1);

namespace ExampleTest;

use Example\Repo\InvoiceRepo\FakeInvoiceRepo;
use ExampleTest\ServerTest;
use ExampleTest\BuiltinServer;
use Example\CliController\PdfGenerator;


class InvoiceTest extends BaseTestCase
{
    public function testInvoiceRenders()
    {
        // Clear the local file cache
        // request an invoice
          // check
        $path = __DIR__ . '/../../app/public';

        [$statusCode, $contents, $responseHeaders] = fetchUri(
            'http://local.app.basereality.com/invoice/0/render',
            'GET'
        );

        // TODO - check body contains appropriate strings.
        $this->assertEquals(200, $statusCode);
    }

    public function testInvoicePdfRenders()
    {
        $pdfGenerator = createPdfGeneratorFromConfig();
        $outputFilename = __DIR__ . '/../../var/tmp_test/testInvoicePdfRenders.pdf';

        @unlink($outputFilename);
        $this->assertFalse(file_exists($outputFilename));

        $pdfGenerator->renderUrlAsPdf(
            'http://local.app.basereality.com' . '/invoice/0/render',
            $outputFilename
        );

        $this->assertTrue(file_exists($outputFilename));
    }

//    public function testInvoicePdfGeneratedThroughQueue()
//    {
//        $generated = false;
//        $domain = 'http://local.app.basereality.com';
//
//        $invoiceRepo = new FakeInvoiceRepo();
//        $invoice = $invoiceRepo->getInvoice(0);
//
//        $pdfUrl = null;
//
//
//
//        for($i=0; $i < 20 && $generated === false; $i++) {
//            [
//                $statusCode,
//                $body,
//                $headers
//            ] = fetchUri($domain . '/invoice/0/generate', 'GET');
//
//            if ($statusCode === 200) {
//                $generated = true;
//                $data = json_decode_safe($body);
//                $this->assertEquals(
//                    buildInvoiceDownloadLink($invoice),
//                    $data['url']
//                );
//
//                $pdfUrl = $data['url'];
//                break;
//            }
//
//            $this->assertEquals(420, $statusCode);
//
//            usleep(100000); // 1/10th of a second
//        }
//
//        [
//            $statusCode,
//            $body,
//            $headers
//        ] = fetchUri($domain . $pdfUrl, 'GET');
//
//        echo "pdf bytes are []" . substr($body, 0, 8);
//
//        // TODO image comparison with Imagick.
//    }
}
