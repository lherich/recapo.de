require(['jquery', 'fuelux'], function($){
  $('a.checkbox').click(function(event){
    if($(this).hasClass('active')) {
      $(this).removeClass('active');
      $('input[name="'+this.id+'"]').prop('checked', false);
      $('input[name="'+this.id+'"]').checkbox('uncheck');
    } else {
      $(this).addClass('active');
      $('input[name="'+this.id+'"]').prop('checked', true);
      $('input[name="'+this.id+'"]').checkbox('check');

    }
  })
});
