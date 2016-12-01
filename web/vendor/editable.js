require(['bootstrap-editable'], function () {
  // bootstrap-editable
  $.fn.editable.defaults.mode = 'popup';  
  $.fn.editable.defaults.emptytext = 'Kein Inhalt';  
   $('.editable.flag').editable({
      source: [
      {value: 'public', text: 'Veröffentlicht'},
      {value: 'protected', text: 'Entwurf'}
      ]
    });
   
  $('.editable').editable();
});