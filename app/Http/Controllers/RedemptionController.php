<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\EmailRedeem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class RedemptionController extends Controller
{
    public function redeemCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'redemption_code' => ['required'],
            'email' => ['required', 'email'],
        ])->setAttributeNames([
            'redemption_code' => 'Redemption Code',
            'email' => 'Email',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 0,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $redemptionCode = $request->redemption_code;
            $email = $request->email;
            $now = Carbon::now()->format('Y-m-d');
            $expire_date = Carbon::now()->addYear()->format('Y-m-d');
            $code2 = $redemptionCode . '_' . $expire_date;
            $serial_number = base64_encode($code2);
            $checker = Code::where('redemption_code', $redemptionCode)->first();
            
            $data = [
                'email' => $email,
                'serial_number' => $serial_number,
                'expire_date' => $expire_date,
                'title' => 'VE-MB28-Redemption'
            ];

            if (empty($checker)){
                return response()->json([
                    'status' => 2,
                    'msg' => 'Invalid redemption code.',
                ]);
            } elseif ($checker->status == 'valid') {
                $checker->update([
                    'status' => 'redeemed',
                    'expired_date' => $expire_date,
                ]);
            } elseif ($checker->status == 'redeemed') {          
                return response()->json([
                    'status' => 2,
                    'msg' => 'Code has already been redeemed.',
                ]);
                
            } elseif ($checker->status == 'expired') {
                return response()->json([
                    'status' => 2,
                    'msg' => 'Code has already expired.',
                ]);
            }

            Mail::send('emails', ['data1' => $data], function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject($data['title']);
            });

            EmailRedeem::create([
                'code_id' => $checker->id,
                'email' => $email,
            ]);

            return response()->json([
                'status' => 1,
                'msg' => 'Code redemption successful. Please check your email for the serial number we sent. Serial Number : ' . $serial_number . '. Expire Date : ' . $expire_date,
            ]);
            
        }
    }
}
