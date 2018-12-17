/*jslint evil: false, vars: true, eqeq: true, white: true */

var AsyncInvoiceDownload = {

    invoiceUrl: null,
    // currentRequest: null,
    errorTimeout: null,
    downloadingCancelled: false,

    setDownloadButton: function() {
      $(this.element).html("<button class='invoice_download_button'>Download</button>");
      $(this.element).find(".invoice_download_button").click(
        $.proxy(this, 'startDownload')
      );
    },

    invoiceGenerated: function(data, textStatus, jqXHR) {
        if (data.status === 'generated') {

          if (this.errorTimeout) {
            clearTimeout(this.errorTimeout);
            this.errorTimeout = null;
          }

          this.setDownloadButton();
          if (data.url) {
            // TODO - is it worth opening a new window?
            window.location = data.url;
            return;
          }
        }

      // Retry to see if the invoice is ready after 100ms
      setTimeout($.proxy(this, 'startRequest'), 100);
    },

    invoiceErrorResponse: function() {
      // Retry to see if the invoice is ready after 100ms
      setTimeout($.proxy(this, 'startRequest'), 100);
    },

    cancelDownload: function() {
      this.downloadingCancelled = true;
      this.setDownloadButton();
      $(this.element).prepend("<span>There was an error downloading</span>");
    },

    startRequest: function() {
      if (this.downloadingCancelled !== false) {
        return;
      }

      $.ajax({
        url: this.invoiceUrl,
        context: this,
        cache: false,
        success: this.invoiceGenerated,
        error: $.proxy(this, 'invoiceErrorResponse')
      });
    },

    startDownload: function() {
      this.downloadingCancelled = false;
      $(this.element).html("<span>generating</span>");
      this.startRequest();
      this.errorTimeout = setTimeout($.proxy(this, 'cancelDownload'), 2 * 1000);
    },
    
    _create: function() {
    },

    _init: function() {
        this.invoiceUrl = $(this.element).data('invoice_url');
        if (!this.invoiceUrl) {
            return;
        }

        this.setDownloadButton();
    }
};

// create the widget
$.widget("opensourcefees.asyncInvoiceDownload", AsyncInvoiceDownload);

function initInvoiceDown(selector) {
    $(selector).asyncInvoiceDownload({});
}
