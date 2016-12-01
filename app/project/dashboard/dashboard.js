require(['bootstrap-switch'], function(){
    $('.flagCheckbox').bootstrapSwitch();
    $('.flagCheckbox').on(
      'switchChange.bootstrapSwitch',
      function(event, state) {
          if(state) {
              $.ajax($(this).data('on-url'));
          } else {
              $.ajax($(this).data('off-url'));
          }
    });
});
