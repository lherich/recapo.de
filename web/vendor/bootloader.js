require(['jquery', 'bootstrap', 'bootstrap-select', 'helper'], function($) {
  var rebind = function() {
    $('.focus').focus();
    // bootstrap-selectpicker
    $('select').selectpicker(); 
  };

  $(document).ajaxComplete(rebind);
  rebind();
  
  $('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
  });
  
  $('ul.nav a[name="'+CURRENT_NAME+'"]').parent('li').addClass('active');
  

  $('#selectProject').change(function(event){
      var href = $(this).val();
      if(isNaN(href))
          window.location.href = href;
  });

  // Javascript to enable link to tab
  var url = document.location.toString();
  if (url.match('#')) {
      $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show');
  } 

  // Change hash for page-reload
  $('.nav-tabs a').on('shown', function (e) {
      window.location.hash = e.target.hash;
  })
});