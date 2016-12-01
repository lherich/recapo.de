require(['datatables-de', 'jquery-ui', 'datatables', 'vendor/editable'], function (datatables_de) {

    $('.editable.reloadOnSave').on('save', function(e, params) {
        location.reload();
    });
    // dataTable
    $('.dataTable').dataTable({
        "language": datatables_de
    });
});
