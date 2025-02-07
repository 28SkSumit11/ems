<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    function index(){
        $data = EntryDetails::where('form_id', 5)->get();
        return view('index', ['data' => $data]);

    }
}
