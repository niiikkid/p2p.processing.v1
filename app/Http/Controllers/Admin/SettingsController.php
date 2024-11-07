<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdatePrimeTimeBonusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $primeTimeBonus = services()->settings()->getPrimeTimeBonus()->toArray();
        $supportLink = services()->settings()->getSupportLink();

        return Inertia::render('Settings/Index', compact('primeTimeBonus', 'supportLink'));
    }

    public function updatePrimeTimeBonus(UpdatePrimeTimeBonusRequest $request)
    {
        services()->settings()->updatePrimeTimeBonus(
            starts: $request->starts,
            ends: $request->ends,
            rate: $request->rate,
        );

        return redirect()->route('admin.settings.index');
    }

    public function updateSupportLink(Request $request)
    {
        $request->validate(['support_link' => 'required', 'url:https']);

        services()->settings()->updateSupportLink($request->support_link);

        return redirect()->route('admin.settings.index');
    }
}
