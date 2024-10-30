<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\API\Dispute\StoreRequest;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DisputeController extends Controller
{
    public function index()
    {
        $orders = queries()
            ->order()
            ->getForAdminApiDisputeCreate()
            ->transform(function (Order $order) { //TODO этим должен заниматься фронт
                $data['id'] = $order->id;
                $data['name'] = '#'.$order->id . ' ' . $order->amount->toBeauty() . ' ' . strtoupper($order->currency->getCode());

                return $data;
            })
            ->toArray();

        return Inertia::render('Admin/API/Dispute/Index', compact('orders'));
    }

    public function store(StoreRequest $request)
    {
        //TODO
        $receipt = $request->file('receipt');

        $data = $request->only('receipt');
        $data['receipt'] = base64_encode(file_get_contents($receipt->getRealPath()));

        $order = Order::find($request->order);

        $result = Http::asMultipart()
            ->withoutVerifying()
            ->withHeaders([
                'Access-Token' => $order->merchant->user->api_access_token,
                'Accept' => 'application/json',
            ])
            ->timeout(15)
            ->post(
                url: /*'https://p2p.payment.local:8891'.*/route('api.dispute', $request->order, absolute: true), //TODO
                data: $data
            );

        if ($result->failed()) {
            return redirect()->route('admin.api.disputes.index')->with(['message' => $result->json()['message']]);
        }

        return redirect()->route('admin.disputes.index');
    }
}
