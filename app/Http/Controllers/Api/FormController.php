<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListingRequest;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function coinListingRequest(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = validator()->make(request()->all(), [
            'coin_name' => 'required|filled',
            'network_info' => 'required|filled',
            'contract_adress' => 'required|filled',
            'coin_site' => 'required|filled',
            'whitepaper_url' => 'required|filled',
            'roadmap_url' => 'required|filled',
            'project_info' => 'required|filled',
            'maximum_supply' => 'required|filled|numeric|min:0000000000000000.0000000000000000000001|max:9999999999999999.9999999999999999999999',
            'listing_exchanges' => 'required|filled',
            'github_url' => 'required|filled',
            'coinmarketcap_url' => 'required|filled',
            'coingecko_url' => 'required|filled',
            'twitter_url' => 'required|filled',
            'telegram_url' => 'required|filled',
            'info' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        try {
            ListingRequest::create($request->toArray());
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.coin_listing_request_success_message')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }

    }
}
