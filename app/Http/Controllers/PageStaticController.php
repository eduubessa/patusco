<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PageStaticController extends Controller
{
    //
    public function home()
    {
        return Inertia::render('Home');
    }
}
