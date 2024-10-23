<?php

namespace App\Http\Controllers;

use App\Http\Requests\Merchant\StoreRequest;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::query()
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->paginate(10);

        $merchants = MerchantResource::collection($merchants);

        return Inertia::render('Merchant/Index', compact('merchants'));
    }

    public function show(Merchant $merchant)
    {
        return Inertia::render('Merchant/Show', compact('merchant'));
    }

    public function create()
    {
        return Inertia::render('Merchant/Add');
    }

    public function store(StoreRequest $request)
    {
        Merchant::create([
            'access_token' => Str::lower(Str::random(32)),
            'user_id' => auth()->id(),
            'active' => true,
        ] + $request->validated());

        return redirect()->route('merchants.index');
    }
}
