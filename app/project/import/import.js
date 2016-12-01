require(['jquery', 'jquery-form'], function($){

  var progressBar = '.progress-bar';

  var progressBarFormSection = '#section form';
  $(progressBarFormSection).ajaxForm({
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

  var progressBarFormItem = '#item form';
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

  var progressBarFormContainer = '#container form';
  $(progressBarFormContainer).ajaxForm({
    beforeSend: function() {
      $(progressBarFormContainer).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormContainer).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormContainer).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormContainer).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormContainer).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
      } else {
        $(progressBarFormContainer).find(':input').attr('disabled', false);
        $(progressBarFormContainer).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

  var progressBarFormIA = '#ia form';
  $(progressBarFormIA).ajaxForm({
    beforeSend: function() {
      $(progressBarFormIA).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormIA).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormIA).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormIA).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormIA).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
      } else {
        $(progressBarFormIA).find(':input').attr('disabled', false);
        $(progressBarFormIA).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

  var progressBarFormTask = '#task form';
  $(progressBarFormTask).ajaxForm({
    beforeSend: function() {
      $(progressBarFormTask).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormTask).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormTask).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormTask).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormTask).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
      } else {
        $(progressBarFormTask).find(':input').attr('disabled', false);
        $(progressBarFormTask).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

});
