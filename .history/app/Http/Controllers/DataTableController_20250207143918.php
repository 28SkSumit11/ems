<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;

class DataTableController extends Controller
{
    function index(){
        $data = EntryDetails::where('form_idd', 5)->get();
        return view('index', compact('data'));

    }
}
