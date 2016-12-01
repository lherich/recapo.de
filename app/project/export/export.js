require(['jquery', 'jquery-form'], function($){

  var progressBarFormSection = '#section form';
  var progressBarFormItem = '#item form';
  var progressBar = '.progress-bar';

  $('#section form').ajaxForm({
    beforeSend: function() {
      $(progressBarFormSection).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormSection).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormSection).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormSection).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormSection).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
      } else {
        $(progressBarFormSection).find(':input').attr('disabled', false);
        $(progressBarFormSection).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

  $(progressBarFormItem).ajaxForm({
    beforeSend: function() {
      $(progressBarFormItem).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormItem).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormItem).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormItem).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormItem).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
      } else {
        $(progressBarFormItem).find(':input').attr('disabled', false);
        $(progressBarFormItem).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

});
