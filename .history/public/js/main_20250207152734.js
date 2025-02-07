jQuery(function () {
    jQuery('#example').DataTable();
    jQuery('#example tr').each(function () {
        console.log(jQuery(this).data('arrJs'));
    });
})
