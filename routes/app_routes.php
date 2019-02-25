<?php


// Each row of this array should return an array of:
// - The path to match
// - The method to match
// - The route info
// - (optional) A setup callable to add middleware/DI info specific to that route
//
// This allows use to configure data per endpoint e.g. the endpoints that should be secured by
// an api key, should call an appropriate callable.
return [
    ['/books', 'GET', 'Example\AppController\Books::index'],

    ['/iframe/container', 'GET', 'Example\AppController\IframeExample::iframeContainer'],
    ['/iframe/contents', 'GET', 'Example\AppController\IframeExample::iframeContents'],

    ['/invoices', 'GET', 'Example\AppController\Invoice::listInvoices'],

    ['/invoice/{invoice_id:.+}/render', 'GET', 'Example\AppController\Invoice::renderInvoice'],

    ['/invoice/{invoice_id:.+}/generate', 'GET', 'Example\AppController\Invoice::generateOrGetDownloadLink'],

    ['/invoice/{invoice_id:.+}/download', 'GET', 'Example\AppController\Invoice::downloadInvoice'],

    ['/twig_index', 'GET', 'Example\AppController\Index::getTwig'],

    ['/word_search', 'GET', 'Example\AppController\Pages::wordSearch'],


    ['/', 'GET', 'Example\AppController\Index::get'],

    ['/{any:.*}', 'GET', 'Example\AppController\Index::get404'],
];
