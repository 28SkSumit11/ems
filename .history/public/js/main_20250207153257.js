jQuery(function () {
    jQuery('#example').DataTable();
    var arrTable = [];
    jQuery('#example tbody tr').each(function () {
        arrTable.push(jQuery(this).data('arrjs'));
    });

    const res = Object.groupBy(arrTable, {{ submission_id }});


})
