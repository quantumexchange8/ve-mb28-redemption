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

            $checker = Code::where('redemption_code', $redemptionCode)->first();
            $acc_name = empty($checker->acc_name) ? null : '&' . $checker->acc_name;
            $broker_name = empty($checker->broker_name) ? null : '*' . $checker->broker_name;
            $license_name = empty($checker->license_name) ? null : '@' . $checker->license_name;

            // Check license name, explode '_' and find the valid year in SettingLicense
            $license_name = ltrim($license_name, '@');

            // Split the license name by underscore
            $license_parts = explode('_', $license_name);

            // Initialize an array to store valid licenses
            $valid_licenses = [];

            // Iterate through each license part to validate
            foreach ($license_parts as $part) {
                $setting_license = SettingLicense::where('slug', $part)->first();

                if ($setting_license) {
                    // Valid license found
                    $valid_licenses[] = $setting_license;
                }
            }

            $expired_date = null;
            $fibo_license = null;
            $mb_license = null;

            foreach ($valid_licenses as $license) {
                if ($license->category === 'FIBO') {
                    $fibo_license = $license;
                } elseif ($license->category === 'MB') {
                    $mb_license = $license;
                }
            }

            if ($mb_license) {
                // If there's an MB license, calculate the expired date based on its valid year
                $expired_date = now()->addYears($mb_license->valid_year);
            } elseif ($fibo_license) {
                // If there's only a FIBO license, calculate the expired date based on its valid year
                $expired_date = now()->addYears($fibo_license->valid_year);
            }

            $code2 = $redemptionCode . $broker_name . $acc_name . $license_name . '#' . $expired_date->format('Ymd');
            $serial_number = base64_encode($code2);

            $data = [
                'email' => $email,
                'serial_number' => $serial_number,
                'expire_date' => $expired_date->format('Y-m-d'),
                'title' => 'VE-MB28-Redemption',
            ];

            if (empty($checker)){
                return response()->json([
                    'status' => 2,
                    'msg' => 'Invalid redemption code.',
                ]);
            } elseif ($checker->status == 'valid') {
                $checker->update([
                    'status' => 'redeemed',
                    'expired_date' => $expired_date->format('Y-m-d'),
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

            Mail::send('emails', ['data1' => $data, 'licenses' => $valid_licenses], function ($message) use ($data) {
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
                'msgDate' => 'Expire Date : ' . $expired_date->format('Y-m-d'),
            ]);

        }
    }
}
