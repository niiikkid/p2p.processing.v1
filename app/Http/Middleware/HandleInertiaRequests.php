<?php

namespace App\Http\Middleware;

use App\Enums\DisputeStatus;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OrderStatus;
use App\Http\Resources\WalletResource;
use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\Order;
use App\Services\Money\Currency;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $orderQuery = Order::query()
            ->where('status', OrderStatus::PENDING);
        $disputeQuery = Dispute::query()
            ->where('status', DisputeStatus::PENDING);

        $userId = auth()->id();
        $userRole = isRouteFor('Merchant') ? 'merchant' : (isRouteFor('Trader') ? 'trader' : (isRouteFor('Super Admin') ? 'admin' : 'guest'));

        $pendingOrdersCount = cache()->remember("pending_orders_{$userRole}_{$userId}", 15, function () use ($orderQuery, $userRole, $userId) {
            if ($userRole === 'merchant') {
                return 0;
            } elseif ($userRole === 'trader') {
                return $orderQuery->clone()->whereRelation('paymentDetail', 'user_id', $userId)->count();
            } elseif ($userRole === 'admin') {
                return $orderQuery->clone()->count();
            } else {
                return 0;
            }
        });

        $pendingDisputesCount = cache()->remember("pending_disputes_{$userRole}_{$userId}", 15, function () use ($disputeQuery, $userRole, $userId) {
            if ($userRole === 'merchant') {
                return 0;
            } elseif ($userRole === 'trader') {
                return $disputeQuery->clone()->whereRelation('order.paymentDetail', 'user_id', $userId)->count();
            } elseif ($userRole === 'admin') {
                return $disputeQuery->clone()->count();
            } else {
                return 0;
            }
        });

        $pendingWithdrawalsCount = cache()->remember("pending_withdrawals_{$userRole}_{$userId}", 15, function () use ($disputeQuery, $userRole, $userId) {
            if ($userRole === 'admin') {
                return Invoice::query()
                    ->where('status', InvoiceStatus::PENDING)
                    ->where('type', InvoiceType::WITHDRAWAL)
                    ->count();
            } else {
                return 0;
            }
        });

        $menu = [
            'pendingOrdersCount' => (int)$pendingOrdersCount,
            'pendingDisputesCount' => (int)$pendingDisputesCount,
            'pendingWithdrawalsCount' => (int)$pendingWithdrawalsCount,
        ];

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'role' => $request->user()?->roles()?->first(),
                'is_admin' => $request->user()?->hasRole('Super Admin')
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'logo' => fn () => $request->user() ? services()->settings()->hasLogo() : false,
            'data' => [
                'rates' => fn () => Currency::getAll()
                    ->transform(function ($currency) {
                        return [
                            'code' => $currency->getCode(),
                            'buy_price' => services()->market()->getBuyPrice($currency)->toPrecision(),
                            'sell_price' => services()->market()->getSellPrice($currency)->toPrecision(),
                        ];
                    })->toArray(),
                'wallet' => fn () => $request->user() ? WalletResource::make($request->user()->wallet)->resolve() : null
            ],
            'menu' => $menu
        ];
    }
}
