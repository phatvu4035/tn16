<?php

namespace App\Http\Controllers;

use App\Facades\Topica;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index()
    {
        Topica::canOrRedirect('index.tn');
        return view('summary.index');
    }
}
