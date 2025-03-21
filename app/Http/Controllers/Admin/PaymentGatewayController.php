<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DetailType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentGateway\StoreRequest;
use App\Http\Requests\Admin\PaymentGateway\UpdateRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Services\Money\Currency;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $paymentGateways = queries()->paymentGateway()->paginateForAdmin();

        $paymentGateways = PaymentGatewayResource::collection($paymentGateways);

        return Inertia::render('PaymentGateway/Index', compact('paymentGateways'));
    }

    public function create()
    {
        $currencies = Currency::getAll()->transform(function ($currency) {
            return ['code' => strtoupper($currency->getCode())];
        })->toArray();

        $detailTypes = [];
        foreach (DetailType::values() as $detailType) {
            $detailTypes[] = [
                'name' => trans('detail-type.'.$detailType),
                'code' => $detailType,
            ];
        }

        $paymentGateways = PaymentGatewayResource::collection(queries()->paymentGateway()->getAllActive())->resolve();

        return Inertia::render('PaymentGateway/Add', compact('currencies', 'detailTypes', 'paymentGateways'));
    }

    public function store(StoreRequest $request)
    {
        PaymentGateway::create($request->validated());

        return redirect()->route('admin.payment-gateways.index');
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        $currencies = Currency::getAll()->transform(function ($currency) {
            return ['code' => strtoupper($currency->getCode())];
        })->toArray();

        $detailTypes = [];
        foreach (DetailType::values() as $detailType) {
            $detailTypes[] = [
                'name' => trans('detail-type.'.$detailType),
                'code' => $detailType,
            ];
        }

        $paymentGateways = queries()->paymentGateway()->getAllActive();
        $paymentGatewaysForSBP = $paymentGateways->filter(function (PaymentGateway $paymentGateway) {
            return $paymentGateway->currency->getCode() === Currency::RUB()->getCode()
                && $paymentGateway->code !== 'sbp_rub';
        });
        $paymentGateways = PaymentGatewayResource::collection($paymentGateways)->resolve();
        $paymentGatewaysForSBP = PaymentGatewayResource::collection($paymentGatewaysForSBP)->resolve();

        $paymentGateway->code = preg_replace('/_[a-z]{1,}$/', '', $paymentGateway->code);

        $paymentGateway = PaymentGatewayResource::make($paymentGateway)->resolve();

        return Inertia::render('PaymentGateway/Edit', compact('paymentGateway', 'currencies', 'detailTypes', 'paymentGateways', 'paymentGatewaysForSBP'));
    }

    public function update(UpdateRequest $request, PaymentGateway $paymentGateway)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($paymentGateway->logo) {
                Storage::disk('public')->delete($paymentGateway->logo);
            }
            
            $path = $request->file('logo')->store('payment-gateways', 'public');
            $data['logo'] = $path;
        }

        $paymentGateway->update($data);

        return redirect()->route('admin.payment-gateways.index');
    }
}
