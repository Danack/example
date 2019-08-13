


var AsyncImageWidget = {

  errorTimeout: null,
  filename: null,
  job_id: 0,

  startImageProcess: function() {
    var text = $("#image_text_input").val();

    $.ajax({
       url: "http://local.app.basereality.com/image_queue",
       context: this,
       cache: false,
       success: $.proxy(this, 'successResponse'),
       // error: $.proxy(this, 'errorResponse'),
       data: {
         text:    text
       },
       method: 'POST',
       timeout: 10 * 1000
    });
  },

  getImageStatus: function () {
    $.ajax({
      url: "http://local.app.basereality.com/image_status",
      context: this,
      cache: false,
      success: $.proxy(this, 'statusSuccessResponse'),
      // error: $.proxy(this, 'errorResponse'),
      data: {
        job_id:    this.job_id
      },
      method: 'GET',
      timeout: 10 * 1000
    });
  },

  statusSuccessResponse: function (data) {

    if (data.job_status === 'complete') {
      this.setImage(this.filename);
      return;
    }

    $(".result_holder").text(data.job_status);
    setTimeout($.proxy(this, 'getImageStatus'), 100)
  },

  successResponse: function (data) {
    if (data.job_status === "queued") {
      this.job_id = data.id;
      this.filename = data.filename;
      setTimeout($.proxy(this, 'getImageStatus'), 100)
    }
    else if (data.job_status === "done") {
      this.setImage(data.filename);
    }
  },

  setImage: function (srcFile) {
    var imgElement = $('<img />').attr({
      'src': '/image/' + srcFile,
      // 'alt': 'JSFiddle logo',
    }).appendTo(".result_holder");
  },

  _create: function() {
  },

  _init: function() {
    $('#image_text_submit').click($.proxy(this, 'startImageProcess'));
  }
};

// create the widget
$.widget("example.asyncImageWidget", AsyncImageWidget);

$('.image_text_widget').asyncImageWidget({});

