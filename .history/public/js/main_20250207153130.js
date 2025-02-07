jQuery(function () {
    jQuery('#example').DataTable();
    var arrTable = [];
    jQuery('#example tbody tr').each(function () {
        arrTable.push(jQuery(this).data('arrjs'));
    });
})
