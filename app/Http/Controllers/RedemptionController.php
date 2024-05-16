<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\EmailRedeem;
use App\Models\SettingLicense;
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

            // VE*Tradehall&lee nic@MB28_MBTrade_FiboR28#20240514
            $checker = Code::where('redemption_code', $redemptionCode)->first();
            $acc_name = empty($checker->acc_name) ? null : '&' . $checker->acc_name;
            $broker_name = empty($checker->broker_name) ? null : '*' . $checker->broker_name;
            $setting_license_name = empty($checker->setting_license_name) ? null : '@' . $checker->setting_license_name;
            $setting_license = SettingLicense::find($checker->setting_license_id);
            $expire_date = Carbon::now()->addYears($setting_license->valid_year);

            $code2 = $redemptionCode . $broker_name . $acc_name . $setting_license_name . '#' . $expire_date->format('Ymd');
            $serial_number = base64_encode($code2);

            $data = [
                'email' => $email,
                'serial_number' => $serial_number,
                'expire_date' => $expire_date->format('Y-m-d'),
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
                    'expired_date' => $expire_date->format('Y-m-d'),
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
                'msg' => 'Code redemption successful.',
                'msgSerial' => 'Serial Number : ' . $serial_number,
                'msgDate' => 'Expire Date : ' . $expire_date->format('Y-m-d'),
            ]);

        }
    }
}
