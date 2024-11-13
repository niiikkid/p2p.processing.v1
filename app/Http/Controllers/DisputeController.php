<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dispute\CancelRequest;
use App\Http\Resources\DisputeResource;
use App\Models\Dispute;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DisputeController extends Controller
{
    public function index()
    {
        $disputes = queries()->dispute()->paginateForUser(auth()->user());

        $disputes = DisputeResource::collection($disputes);

        return Inertia::render('Dispute/Index', compact('disputes'));
    }

    public function accept(Dispute $dispute)
    {
        Gate::authorize('access-to-dispute', $dispute);

        services()->dispute()->accept($dispute);
    }

    public function cancel(CancelRequest $request, Dispute $dispute)
    {
        Gate::authorize('access-to-dispute', $dispute);

        services()->dispute()->cancel($dispute, $request->reason);
    }

    public function rollback(Dispute $dispute)
    {
        services()->dispute()->rollback($dispute);
    }

    public function receipt(Dispute $dispute)
    {
        Gate::authorize('access-to-dispute-receipt', $dispute);

        $file_path = storage_path('receipts/'.$dispute->receipt);

        return response()->file($file_path);
    }
}
