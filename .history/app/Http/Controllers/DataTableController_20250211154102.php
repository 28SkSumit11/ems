<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class DataTableController extends Controller
{
    public function index($form_id = 5){
        // dd($dj);
        return view('index');

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $form_id = $request->get('form_id');
            $dateRange = $request->get('date_range');
            $start_date = $request->get('start_date'); // Example: "2025-01-01"
            $end_date = $request->get('end_date');     // Example: "2025-01-15"

            // $form_id = 8;
            // $dateRange = '5';
            // $start_date = "2025-01-01"; // Example: "2025-01-01"
            // $end_date = "2025-01-15";   // Example: "2025-01-15"


            // Fetch data from the database
            $query = DB::table('wp_fluentform_submissions')
                    ->select('id', 'form_id', 'response', 'created_at')
                    ->where('form_id', $form_id);

            // Apply filtering based on the selected date range
            if ($dateRange == 'custom' && $start_date && $end_date) {
                [$startDate, $endDate] = $this->getDateRange($dateRange, $start_date, $end_date);
                $query->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'DESC');
            } else {
                [$startDate, $endDate] = $this->getDateRange($dateRange);
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'DESC');
                }
            }


            // Fetch and group the data
            $totalRecords = $query->distinct('form_id')->count();


            $data = $query->get();

            // Calculate total records for pagination
            // $totalRecords = $query->distinct('submission_id')->count();

            // Format the data to group by submission_id
            $formattedData = [];
            foreach ($data as $entry) {
                $submission_id = $entry->id;
                $response = json_decode($entry->response);
                if (isset($submission_id)) {
                    $date = $response->datetime; // Input date in DD-MM-YYYY format
                    if (Carbon::hasFormat($date, 'd-m-Y')) {
                        $formattedDate = Carbon::createFromFormat('d-m-Y', $date)->format('d-M-Y');
                    } else {
                        $formattedDate = $date; // If not in the expected format, use the original date
                    }
                    $formattedData[$submission_id] = [
                        'id' => $submission_id,
                        'name' => $response->names->first_name,
                        'datetime' => $formattedDate,
                        'action' => '<i class="fa-solid fa-eye view" data-subId="' . $submission_id . '"></i>',
                    ];

                }

            }

            // Get DataTable parameters
            $draw = $request->get('draw');
            // $start = 0;
            // $length = 10;
            $start = $request->get('start'); // Start index
            $length = $request->get('length'); // Number of records per page
            $searchValue = $request->get('search')['value']; // Search value
            $orderColumnIndex = $request->get('order')[0]['column'] ?? 0; // Column index to sort
            $orderDirection = $request->get('order')[0]['dir'] ?? 'asc'; // Sort direction (asc/desc)

            $columns = ['id', 'name', 'datetime', 'action'];
            $orderBy = $columns[$orderColumnIndex] ?? 'id';


            if (!empty($searchValue)) {
                $formattedData = array_filter($formattedData, function ($row) use ($searchValue) {
                    return
                        stripos((string) $row['id'], $searchValue) !== false ||
                        stripos($row['name'], $searchValue) !== false ||
                        stripos($row['datetime'], $searchValue) !== false;
                });
            }


            usort($formattedData, function ($a, $b) use ($orderBy, $orderDirection) {
                return $orderDirection === 'asc'
                    ? strcmp($a[$orderBy], $b[$orderBy])
                    : strcmp($b[$orderBy], $a[$orderBy]);
            });


            $paginatedData = array_slice($formattedData, $start, $length);
            session(['formattedData' => $formattedData]);

            // Pagination
            $totalRecords = count($formattedData);
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => array_values($paginatedData),
            ]);
            // dd($dj);
        }
    }

    private function getDateRange($dateRange, $startDate = null, $endDate = null)
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
            case 'custom':
                return [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
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

            '8' =>
            ['dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'COVERAGE TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'INSSUER NAME', 'datetime' => 'ISSUE DATE', 'input_text' => 'REGISTRATION NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'KYC-ID PROOF', 'file-upload_1' => 'KYC-ADDRESS PROOF OF PROPOSER', 'file-upload_3' => 'VEHICLE RC (FRONT)', 'file-upload_4' => 'VEHICLE RC (BACK)', 'file-upload_5' => 'PYP', 'file-upload_6' => 'POLICY COPY', 'file-upload_8' => 'INVOICE COPY', 'file-upload_9' => 'INVOICE COPY', 'file-upload_11' => 'POLICY COPY', 'file-upload_12' => 'ILLUSTRATION', 'file-upload_13' => 'PROPOSAL FORM', 'file-upload_14' => 'PROOF OF PAYMENT', 'input_radio' => 'PRODUCT TYPE'],
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

        // foreach ($data as $item) {
        //     $arr = explode('_', $item->field_name);
        //     if(!str_contains($item->field_name, 'email_1')){
        //         if(in_array('file-upload', $arr)){
        //             $row .= '<tr><td>'.$item->field_name.'</td><td><a href="'.$item->field_value.'" target="_blank">Click here</a></td></tr>';
        //         }else{
        //             $row .= '<tr><td>'.$item->field_name.'</td><td>'.$item->field_value.'</td></tr>';
        //         }
        //     }
        // }


        return $row;
    }

    public function exportCSV(Request $request, $form_id){
        $data = session('formattedData');
        if (!$data) {
            return back()->with('error', 'No data available for export.');
        }

        $submissionIds = collect($data)->pluck('id')->toArray();

        $keyArr = ['5' =>
            ['id' => 'Submission Id Number', 'dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'POLICY TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'NAME OF INSURANCE COMPANY', 'datetime' => 'ISSUE DATE', 'input_text_1' => 'POLICY NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'CUSTOMER PAN/GST', 'file-upload_1' => 'KYC OF PROPOSER (PAN/AADHAR - FRONT)', 'file-upload_10' => 'KYC OF PROPOSER (PAN/AADHAR - BACK)', 'file-upload_11' => 'POLICY COPY'],

            '6' =>
            ['id' => 'Submission Id Number', 'dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'POLICY TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'NAME OF INSURANCE COMPANY', 'datetime' => 'ISSUE DATE', 'input_text_1' => 'POLICY NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'CUSTOMER PAN/GST', 'file-upload_1' => 'KYC OF PROPOSER (PAN/AADHAR - FRONT)', 'file-upload_10' => 'KYC OF PROPOSER (PAN/AADHAR - BACK)', 'file-upload_11' => 'POLICY COPY', 'file-upload_12' => 'ILLUSTRATION', 'file-upload_13' => 'PROPOSAL FORM', 'file-upload_14' => 'PROOF OF PAYMENT'],

            '8' =>
            ['id' => 'Submission Id Number', 'dropdown' => "RM NAME", 'dropdown_1' => "POSP NAME", "names" => "CUSTOMER NAME", "dropdown_2" => 'PRODUCT TYPE', 'dropdown_3' => 'COVERAGE TYPE', 'phone' => 'CUSTOMER MOBILE NO', 'email' => 'CUSTOMER MAIL ID', 'dropdown_4' => 'INSSUER NAME', 'datetime' => 'ISSUE DATE', 'input_text' => 'REGISTRATION NUMBER', 'input_text_1' => 'POLICY NUMBER', 'numeric_field_1' => 'NET PREMIUM', 'file-upload' => 'KYC-ID PROOF', 'file-upload_1' => 'KYC-ADDRESS PROOF OF PROPOSER', 'file-upload_3' => 'VEHICLE RC (FRONT)', 'file-upload_4' => 'VEHICLE RC (BACK)', 'file-upload_5' => 'PYP', 'file-upload_6' => 'POLICY COPY', 'file-upload_7' => 'UNKNOWN FIELD', 'file-upload_8' => 'INVOICE COPY', 'file-upload_9' => 'INVOICE COPY', 'input_radio' => 'PRODUCT TYPE'],
        ];

        // Fetch all entries at once
        $entries = DB::table('wp_fluentform_submissions')
            ->whereIn('id', $submissionIds)
            ->get();

        // Extract unique headers
        $header = [];
        $rows = [];
        foreach ($entries as $item) {
            // dd($key, $item);
            $arr = json_decode($item->response);
            $row = [];
            foreach($arr as $key=>$it){
                // dd($keyArr[$form_id], $arr);
                if($key != 'email_1', $key != '__fluent_form_embded_post_id'){
                    $header[] = $keyArr[$form_id][$key];
                    if (is_array($it) && isset($it['first_name'])) {
                        $row[] = $it['first_name'];
                    } else {
                        $row[] = $it;
                    }
                }
            }
            $rows[] = $row;
        }
        dd($header, $rows);
        $filename = 'EMS.csv';

        // Stream CSV file instead of storing in memory
        $handle = fopen('php://output', 'w');
        ob_start();
        $header = array_values(array_intersect_key($keyArr[$form_id], array_flip($entries->pluck('field_name')->unique()->toArray())));
        fputcsv($handle, $header);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        $csvOutput = ob_get_clean();

        return Response::make($csvOutput, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }


}
