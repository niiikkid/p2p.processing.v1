<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceContract;
use App\DTO\SMS\SmsDTO;
use App\Enums\DetailType;
use App\Enums\OrderStatus;
use App\Exceptions\SmsServiceException;
use App\Models\Order;
use App\Models\SmsLog;
use App\Services\Sms\Utils\Parser;

class SmsService implements SmsServiceContract
{
    /**
     * @throws SmsServiceException
     */
    public function handleSms(SmsDTO $sms): void
    {
        $smsLog = $this->logSms($sms);

        $result = (new Parser())->parse($sms->sender, $sms->message);

        if (empty($result)) {
            return;
        }

        $order = queries()
            ->order()
            ->findPendingForSBP($result->amount, $sms->user, $result->paymentGateway);

        if (! $order) {
            if ($result->paymentGateway->payment_confirmation_by_card_last_digits) {
                $orders = queries()
                    ->order()
                    ->findPendingMultipleCardConfirmation($result->amount, $sms->user, $result->paymentGateway, $result->card_last_digits);

                if ($orders->count() > 1) {
                    $order = null;
                } else {
                    $order = $orders->first();
                }
            } else {
                $order = queries()
                    ->order()
                    ->findPending($result->amount, $sms->user, $result->paymentGateway);
            }
        }

        if (! $order) {
            return;
        }

        if ($order && $order->status->equals(OrderStatus::PENDING)) {
            services()->order()->succeed($order);

            $smsLog->update([
                'order_id' => $order->id,
            ]);
        }
    }

    protected function logSms(SmsDTO $sms): SmsLog
    {
        return SmsLog::create([
            'sender' => $sms->sender,
            'message' => $sms->message,
            'timestamp' => $sms->timestamp / 1000,
            'type' => $sms->type,
            'user_id' => $sms->user->id,
        ]);
    }
}
