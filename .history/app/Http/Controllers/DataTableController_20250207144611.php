<?php

namespace App\Http\Controllers;

use App\Models\EntryDetails;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;

class DataTableController extends Controller
{
    function index(){
        $data = EntryDetails::where('form_id', 5)->get();
        $data = DB
        return view('index', ['data' => $data]);

    }
}
