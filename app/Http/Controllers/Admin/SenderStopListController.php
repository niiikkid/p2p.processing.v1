<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SenderStopList;
use App\Models\SmsLog;

class SenderStopListController extends Controller
{
    public function store(SmsLog $smsLog)
    {
        SenderStopList::create([
            'sender' => $smsLog->sender
        ]);

        SmsLog::where('sender', $smsLog->sender)->delete();
    }

    public function destroy(SenderStopList $senderStopList)
    {
        $senderStopList->delete();
    }
}
