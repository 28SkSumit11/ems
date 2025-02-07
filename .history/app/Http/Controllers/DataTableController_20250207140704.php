<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    function index(){
        $data = EntryDetails::all();
        return view('index');

    }
}
