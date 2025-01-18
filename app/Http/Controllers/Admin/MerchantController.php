<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::query()
            ->with('user')
            ->orderByDesc('id')
            ->paginate(10);

        $merchants = MerchantResource::collection($merchants);

        return Inertia::render('Merchant/Index', compact('merchants'));
    }

    public function ban(Merchant $merchant)
    {
        $merchant->update([
            'banned_at' => now(),
            'validated_at' => now(),
        ]);
    }

    public function unban(Merchant $merchant)
    {
        $merchant->update([
            'banned_at' => null,
            'validated_at' => now(),
        ]);
    }

    public function validated(Merchant $merchant)
    {
        $merchant->update([
            'validated_at' => now(),
        ]);
    }

    public function updateSettings(Request $request, Merchant $merchant)
    {
        $request->validate(['make_order_amount_unique' => 'required', 'amount']);

        $merchant->update([
            'make_order_amount_unique' => $request->make_order_amount_unique
        ]);
    }
}
