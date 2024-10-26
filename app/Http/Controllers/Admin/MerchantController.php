<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::query()
            ->orderByDesc('id')
            ->paginate(10);

        $merchants = MerchantResource::collection($merchants);

        return Inertia::render('Admin/Merchant/Index', compact('merchants'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Merchant $merchant)
    {
        //
    }

    public function edit(Merchant $merchant)
    {
        //
    }

    public function update(Request $request, Merchant $merchant)
    {
        //
    }
}
