<?php

namespace App\Http\Controllers;

use App\Models\Applet;
use Illuminate\Http\Request;

class AppletController extends Controller
{
    public function index()
    {
        return Applet::all();
    }

    public function active()
    {
        return Applet::where('status', 'active')->get();
    }
}
