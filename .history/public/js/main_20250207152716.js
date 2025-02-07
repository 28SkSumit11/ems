jQuery(function () {
    jQuery('#example').DataTable();
    jQuery('#example2 tr').each(function () {
        console.log(jQuery(this).data('arrJs'));
    });
})
