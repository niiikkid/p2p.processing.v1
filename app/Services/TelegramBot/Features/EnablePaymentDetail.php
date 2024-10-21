<?php

namespace App\Services\TelegramBot\Features;

use App\Models\PaymentDetail;

class EnablePaymentDetail extends Feature
{
    public function handle(): void
    {
        $telegram = $this->getTelegram();

        if (! $telegram) {
            return;
        }

        $user = $telegram->user;

        $detailID = $this->getArgument('detail_id');

        if (! $detailID) {
            return;
        }

        $paymentDetail = PaymentDetail::find($detailID);

        if (! $paymentDetail || $paymentDetail->user?->id !== $user->id) {
            return;
        }

        $paymentDetail->update(['is_active' => true]);

        $this->callFeature(EditPaymentDetail::class, ['detail_id' => $paymentDetail->id]);
    }
}
