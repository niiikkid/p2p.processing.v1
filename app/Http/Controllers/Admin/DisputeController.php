<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DisputeResource;
use Inertia\Inertia;

class DisputeController extends Controller
{
    public function index()
    {
        $disputes = queries()->dispute()->paginateForAdmin();

        $disputes = DisputeResource::collection($disputes);

        return Inertia::render('Dispute/Index', compact('disputes'));
    }
}
