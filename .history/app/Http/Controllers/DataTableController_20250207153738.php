<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    function index(){

        $data = DB::table('wp_fluentform_entry_details')->where('form_id', 5)->get();
        $arrTable = [];

        foreach ($data as $item) {
            $arrTable[] = $item;
        }

        $res = collect($arrTable)->groupBy('submission_id');

        dd($res);
        return view('index', ['data' => $data]);

    }
}
