$(document).ready(function () {

    $('#dateFilter').on('change', function (e) {
        // console.log(e.type);
        let selectedValue = $(this).val();
        if (selectedValue === 'custom' && $('#customDateInputs').is(':hidden')) {
            console.log('show');
            $(".select-box-section div:first").removeClass('col-md-12').addClass('col-md-10');
            $('.select-box-section #calender').removeClass('d-none');
            $('#customDateInputs').show();
        } else {
            $(".select-box-section div:first").removeClass('col-md-10').addClass('col-md-12');
            $('.select-box-section #calender').addClass('d-none');
            $('#customDateInputs').hide();
            loadTable();
        }
    });

    $("#calender i").click(function () {
        console.log('click');
        $('#customDateInputs').toggle();
    })

    $('#applyCustomRange').click(function () {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        if (startDate && endDate) {
            loadTable(startDate, endDate);
            $("#customDateInputs").hide();
        } else {
            alert("Please select both start and end dates.");
        }
    });

    var dataGetUrl = document.querySelector('meta[name="data-get-url"]').content;
    var formId = $("#form_id").val();

    function loadTable(startDate = null, endDate = null) {
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy(); // Destroy previous DataTable instance
        }
        console.log(startDate, endDate);
        $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataGetUrl,
                data: function (d) {
                    d.form_id = formId;
                    d.date_range = $('#dateFilter').val();
                    d.start_date = startDate;
                    d.end_date = endDate;
                }
            },
            error: function (xhr) {
                console.log("AJAX Error:", xhr.responseText);
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'datetime', name: 'datetime' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            deferRender: true,
            initComplete: function (settings, json) {
                console.log("DataTable table loaded successfully!");
                // View Modal
                $(".view").click(function () {
                    console.log('view');
                    var id = $(this).data('subid');
                    $.ajax({
                        url: '/view/' + id,
                        type: 'GET',
                        success: function (response) {
                            console.log(response);
                            $('#viewModal .modal-body table tbody').html(response);
                            $('#user_id').text("#" + id);
                            $('#viewModal').modal('show');
                        }
                    });
                });
            },
            drawCallback: function (settings) {
                console.log("DataTable draw event fired!");
                // View Modal
                $(".view").click(function () {
                    console.log('view');
                    var formId = $("#form_id").val();
                    var id = $(this).data('subid');
                    $.ajax({
                        url: '/view/' + formId + '/' + id,
                        type: 'GET',
                        success: function (response) {
                            console.log(response);
                            $('#viewModal .modal-body table tbody').html(response);
                            $('#user_id').text("#" + id);
                            $('#viewModal').modal('show');
                        }
                    });
                });
            }
        });

    }

    loadTable();


    $('#dateFilter').change(function () {
        $('#example').DataTable().ajax.reload();
    });

    $("#form_id").change(function () {
        formId = $(this).val();
        $('#example').DataTable().ajax.reload();
    });



});