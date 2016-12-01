require(['moment', 'bootstrap-datetimepicker', 'fuelux', 'bootstrap-validator', 'bootstrap-select'], function(moment) {

    $('form.validate').validator();
    //console.log($('form.validate'));

    var defaultDate;
    if($('#schedulingStartDatetime').val() == '')
        defaultDate = moment().format("MM-DD-YYYY");
    else
        defaultDate = $('#schedulingStartDatetime').val();

    $('#schedulingStartDatetime').datetimepicker({
        defaultDate: defaultDate,
        language: 'de',
        pickTime: false,
        minDate: moment()
    });

    var defaultDate;
    if($('#schedulingStartDatetime').val() == '' && $('#schedulingEndDatetime').val() == '')
        defaultDate = moment().add('d',7).format("MM-DD-YYYY");
    else if ($('#schedulingStartDatetime').val() != '' && $('#schedulingEndDatetime').val() == '')
        defaultDate = moment($('#schedulingStartDatetime').val()).add('d',7).format("MM-DD-YYYY");
    else
        defaultDate = $('#schedulingEndDatetime').val();
    $('#schedulingEndDatetime').datetimepicker({
        defaultDate: moment().add('d',7).format("MM-DD-YYYY"),
        language: 'de',
        pickTime: false,
        minDate: moment().add('d',7)
    });


    $("#schedulingStartDatetime").click(function(){
        $('#schedulingStartDatetime').data("DateTimePicker").show();
    })
    $("#schedulingEndDatetime").click(function(){
        $('#schedulingEndDatetime').data("DateTimePicker").show();
    })

    $("#schedulingStartDatetime").on("dp.change",function (e) {
        var minDate = moment(e.date).add('d', 7);
        $('#schedulingEndDatetime').data("DateTimePicker").setMinDate(minDate);

        // if currentDate < minDate => set minDate
        if(moment($('#schedulingEndDatetime').data('DateTimePicker').getDate()).isBefore(minDate))
            $('#schedulingEndDatetime').data("DateTimePicker").setDate(minDate);
    });


    $('#schedulingEndDatetime').hide();
    $('#schedulingEndSpinbox').hide();
    $('#schedulingEndType').change(function() {
        var val = $(this).val();
        switch(val) {
            case 'no':
                $('#schedulingEndDatetime').hide();
                $('#schedulingEndSpinbox').hide();
                break;
            case 'day':
                $('#schedulingEndDatetime').hide();
                $('#schedulingEndSpinbox').show();
                break;
            case 'point':
                $('#schedulingEndDatetime').show();
                $('#schedulingEndSpinbox').hide();
                break;
        }
    });
    $('#schedulingEndType').trigger($.Event('change'));

    $('#schedulingFlag').click(function(){
      if($('input[name='+this.id+']').checkbox('isChecked')) {
        $('#schedulingFieldset').attr('disabled', true);
        $('#schedulingEndType').prop('disabled',true);
        $('#schedulingEndType').selectpicker('refresh');
      } else {
        $('#schedulingFieldset').removeAttr('disabled');
        $('#schedulingEndType').removeAttr('disabled');
        $('#schedulingEndType').selectpicker('refresh');

      }
    });
});
