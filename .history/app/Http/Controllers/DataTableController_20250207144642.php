<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    function index(){
        $data = EntryDetails::where('form_id', 5)->get();
        $data = DB::table('wp_fluentform_entry_details')->where('form_id', 5)->get();
        return view('index', ['data' => $data]);

    }
}
