require(['jquery', 'fuelux', 'bootstrap-validator'], function($){

  $('#wizardForm').on('changed.fu.wizard', function(e, data) {
    if($('.btn-prev').prop('disabled'))
      $('.btnWizardPrev').hide();
    else
      $('.btnWizardPrev').show();

    if($('.btn-next').html() == 'Speichern') {
      $('.btnWizardNext').hide();
    } else {
      $('.btnWizardNext').show();
    }


  });
  $('.btnWizardPrev').on('click', function() {
    $('#wizardForm').wizard('previous');
  });
  $('.btnWizardNext').on('click', function() {
      $('#wizardForm').wizard('next');
  });
  
  $('#name').on('keyup',function() {
    var name = $('#name').val();
    var url = $('#url').val();
    if(name.substring(0, url.length) == url) {
      //var newurl = $('#experimenturl').data('baseurl') + "/" + $('#name').val();
      //$('#experimenturl').attr('href', newurl);
      //$('#experimenturl').html(newurl);
      $('#url').val($('#name').val());
      $('#url').trigger('keyup');
      $('form.validate').validator('validate');
    }
  });
  $('#url').on('keyup',function() {
      if($('#url').val() == 'backend')
        $('#url').val('backend-kann-nicht-als-url-genutzt-werden');
      var newurl = $('#experimenturl').data('baseurl') + "/" + $('#url').val();
      $('#experimenturl').html(newurl);
      $('#experimenturl').attr('href', newurl);
  });
});