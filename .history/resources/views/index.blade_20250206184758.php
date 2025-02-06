<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
</head>

<body>
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tiger Nixon</td>
                <td>System Architect</td>
                <td>Edinburgh</td>
                <td>61</td>
                <td>2011-04-25</td>
                <td>$320,800</td>
            </tr>
        </tbody>
        <?php
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM wp_fluentform_entry_details");

        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>{$row->id}</td>";
            echo "<td>{$row->email_1}</td>";
            echo "<td>{$row->dropdown}</td>";
            echo "<td>{$row->dropdown_1}</td>";
            echo "<td>{$row->first_name}</td>";
            echo "<td>{$row->dropdown_2}</td>";
            echo "<td>{$row->dropdown_2}</td>";
            echo "<td>{$row->dropdown_2}</td>";
            echo "<td>{$row->dropdown_2}</td>";
            echo "<td>{$row->dropdown_2}</td>";
            echo "</tr>";
        }
        ?>
        <tfoot>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </tfoot>
    </table>


    <script src="/test/ems/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/test/ems/node_modules/datatables.net/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
</body>

</html>
