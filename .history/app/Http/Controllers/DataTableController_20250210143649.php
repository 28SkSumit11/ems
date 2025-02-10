<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DataTableController extends Controller
{
    public function index($form_id = 5){
        // dd($dj);
        return view('index');

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $form_id = $request->form_id;
            $dateRange = $request->date_range;
            $start_date = $request->start_date; // Example: "2025-01-01"
            $end_date = $request->end_date;     // Example: "2025-01-15"

            // $form_id = 8;
            // $dateRange = '8';
            // $start_date = null; // Example: "2025-01-01"
            // $end_date = null;     // Example: "2025-01-15"

            // Convert dates to Carbon instances
            $start = $start_date ? Carbon::parse($start_date)->format('Y-m-d') : null;
            $end = $end_date ? Carbon::parse($end_date)->format('Y-m-d') : null;


            // Fetch data from the database
            $query = DB::table('wp_fluentform_entry_details')
                ->where('form_id', $form_id);

            // Apply filtering based on the selected date range
            if ($dateRange == 'custom' && $start && $end) {
                $query->whereBetween(
                    DB::raw("STR_TO_DATE(field_value, '%d-%b-%y')"),
                    [$start, $end]
                );
            } else {
                [$startDate, $endDate] = $this->getDateRange($dateRange);

                if ($startDate && $endDate) {
                    $query->whereBetween(
                        DB::raw("STR_TO_DATE(field_value, '%d-%b-%y')"),
                        [$startDate, $endDate]
                    );
                }
            }

            // $start = $request->get('start'); // Start index
            // $length = $request->get('length'); // Number of records per page

            $start = 0; // Start index
            $length = 10; // Number of records per page

            // Fetch and group the data
            $totalRecords = $query->distinct('submission_id')->count();
            // dd($totalRecords);

            $submissionIds = $query->distinct('submission_id')
                // ->offset($start)
                // ->limit($length)
                ->pluck('submission_id');

            // Fetch the full data for the given submission_ids
            $data = $query->whereIn('submission_id', $submissionIds)
                ->get();

            // Calculate total records for pagination
            $totalRecords = $query->distinct('submission_id')->count();

            // Format the data to group by submission_id
            $formattedData = [];
            foreach ($data as $entry) {
                if (!isset($formattedData[$entry->submission_id])) {
                    $formattedData[$entry->submission_id] = [
                        'id' => $entry->submission_id,
                        'name' => '',
                        'datetime' => '',
                        'action' => '<i class="fa-solid fa-eye view" data-subId = '.$entry->submission_id.'></i>',
                    ];
                }

                // Fill name and datetime based on field_name
                if ($entry->field_name == 'datetime') {
                    $formattedData[$entry->submission_id]['datetime'] = $entry->field_value;
                }

                if ($entry->field_name == 'names') {
                    $formattedData[$entry->submission_id]['name'] = $entry->field_value;
                }
            }

            // Apply Date Range Filter
            // $formattedData = $this->applyDateFilter($formattedData, $dateRange);

            // ✅ Implement Pagination, Sorting, and Searching
            $totalRecords = count($formattedData);



            // Get DataTable parameters
            $draw = $request->get('draw');
            $start = $request->get('start'); // Start index
            $length = $request->get('length'); // Number of records per page
            $searchValue = $request->get('search')['value']; // Search value
            $orderColumnIndex = $request->get('order')[0]['column'] ?? 0; // Column index to sort
            $orderDirection = $request->get('order')[0]['dir'] ?? 'asc'; // Sort direction (asc/desc)

            // Convert column index to column name
            $columns = ['id', 'name', 'datetime', 'action'];
            $orderBy = $columns[$orderColumnIndex] ?? 'id';

            // ✅ Apply Searching
            if (!empty($searchValue)) {
                $formattedData = array_filter($formattedData, function ($row) use ($searchValue) {
                    return
                        stripos((string) $row['id'], $searchValue) !== false ||
                        stripos($row['name'], $searchValue) !== false ||
                        stripos($row['datetime'], $searchValue) !== false;
                });
            }

            // ✅ Apply Sorting
            usort($formattedData, function ($a, $b) use ($orderBy, $orderDirection) {
                return $orderDirection === 'asc'
                    ? strcmp($a[$orderBy], $b[$orderBy])
                    : strcmp($b[$orderBy], $a[$orderBy]);
            });

            // ✅ Apply Pagination
            $paginatedData = array_slice($formattedData, $start, $length);
            session(['formattedData' => $formattedData]);

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => array_values($paginatedData),
            ]);
        }
    }

    private function getDateRange($dateRange)
    {
        $currentDate = Carbon::now();

        switch ($dateRange) {
            case '1': // Yesterday
                return [Carbon::now()->subDay()->startOfDay(), Carbon::now()->subDay()->endOfDay()];
            case '2': // Last 7 Days
                return [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()];
            case '3': // Last 30 Days
                return [Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay()];
            case '4': // This Month
                return [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()];
            case '5': // Last Month
                return [
                    $currentDate->copy()->subMonth()->startOfMonth(),
                    $currentDate->copy()->subMonth()->endOfMonth()
                ];
            case '6': // This Year
                return [$currentDate->copy()->startOfYear(), $currentDate->copy()->endOfYear()];
            case '7': // Last Year
                return [
                    $currentDate->copy()->subYear()->startOfYear(),
                    $currentDate->copy()->subYear()->endOfYear()
                ];
            case '8': // Lifetime (No Filtering)
                return [null, null];
            default: // Today (Default)
                return [$currentDate->copy()->startOfDay(), $currentDate->copy()->endOfDay()];
        }
    }



    private function applyDateFilter($data, $dateRange)
    {
        $currentDate = Carbon::now();

        $dateFormat = 'd-M-y';

        switch ($dateRange) {
            case '1': // Yesterday
                $startDate = $currentDate->subDay();
                break;
            case '2': // Last 7 Days
                $startDate = $currentDate->subDays(7);
                break;
            case '3': // Last 30 Days
                $startDate = $currentDate->subDays(30);
                break;
            case '4': // This Month
                $startDate = $currentDate->startOfMonth();
                break;
            case '5': // Last Month
                $startDate = $currentDate->subMonth()->startOfMonth();
                break;
            case '6': // This Year
                $startDate = $currentDate->startOfYear();
                break;
            case '7': // Last Year
                $startDate = $currentDate->subYear()->startOfYear();
                break;
            case '8': // Lifetime
                return $data; // No filtering
            case '9': // Custom Range
                // Here you can implement custom date range functionality
                break;
            default: // Today
                $startDate = $currentDate->startOfDay();
                break;
        }

        return array_filter($data, function ($row) use ($startDate, $dateFormat) {
            $datetime = Carbon::parse( $row['datetime']);
            return $datetime->greaterThanOrEqualTo($startDate);
        });
    }


    public function view($form_id, $id){
        $data = DB::table('wp_fluentform_entry_details')->where('submission_id', $id)->get();
        $row = '';
        $keyArr = ['5' =>
            ['dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'POLICY TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'NAME OF INSURANCE COMPANY', 'datetime' => 'ISSUE DATE', 'input_text_1' => 'POLICY NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'CUSTOMER PAN/GST', 'file-upload_1' => 'KYC OF PROPOSER (PAN/AADHAR - FRONT)', 'file-upload_10' => 'KYC OF PROPOSER (PAN/AADHAR - BACK)', 'file-upload_11' => 'POLICY COPY'],

            '6' =>
            ['dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'POLICY TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'NAME OF INSURANCE COMPANY', 'datetime' => 'ISSUE DATE', 'input_text_1' => 'POLICY NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'CUSTOMER PAN/GST', 'file-upload_1' => 'KYC OF PROPOSER (PAN/AADHAR - FRONT)', 'file-upload_10' => 'KYC OF PROPOSER (PAN/AADHAR - BACK)', 'file-upload_11' => 'POLICY COPY', 'file-upload_12' => 'ILLUSTRATION', 'file-upload_13' => 'PROPOSAL FORM', 'file-upload_14' => 'PROOF OF PAYMENT'],
        ];

        foreach ($data as $item) {
            $arr = explode('_', $item->field_name);
            if(!str_contains($item->field_name, 'email_1')){
                if(in_array('file-upload', $arr)){
                    $row .= '<tr><td>'.$keyArr[$form_id][$item->field_name].'</td><td><a href="'.$item->field_value.'" target="_blank">Click here</a></td></tr>';
                }else{
                    $row .= '<tr><td>'.$keyArr[$form_id][$item->field_name].'</td><td>'.$item->field_value.'</td></tr>';
                }
            }
        }

        return $row;
    }

    public function exportCSV(Request $request){
        $data = session('formattedData');
        $header = [];
        $values = [];

        foreach($data as $item){
            $id = $item['id'];
            $entryData = DB::table('wp_fluentform_entry_details')->where('submission_id', $id)->get();

            foreach ($entryData as $entry) {
                if (!in_array($entry->field_name, $header)) {
                    $header[] = $entry->field_name;
                }
                $values[] = $entry->field_value;
            }
        }

        $filename = 'EMS.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, $header);
        fputcsv($handle, $values);

        fclose($handle);

        $headers = [
            'Content-Type' => 'text/csv',
        ];

        return response()->download($filename, 'data.csv', $headers);
    }


}
