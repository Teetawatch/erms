<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkLogController extends Controller
{
    public function index()
    {
        return view('worklogs.index');
    }
}
