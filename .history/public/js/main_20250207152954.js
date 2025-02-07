jQuery(function () {
    jQuery('#example').DataTable();
    jQuery('#example tbody tr').each(function () {
        console.log(jQuery(this).data('arrJs'));
    });
})
