require(['vendor/editable', 'bootstrap-validator', 'bootstrap-editable', 'bootstrap-select'], function() {
  $('form.validate').validator();
  // bootstrap-editable
  $.fn.editable.defaults.mode = 'popup';
  $.fn.editable.defaults.emptytext = 'Kein Inhalt';

  $('.editable.selectItemID').editable({source: items});
});
