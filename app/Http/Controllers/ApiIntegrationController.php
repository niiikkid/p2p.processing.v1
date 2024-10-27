<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ApiIntegrationController extends Controller
{
    public function index()
    {
        return Inertia::render('Integration/Index');
    }
}
