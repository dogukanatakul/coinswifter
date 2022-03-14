<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class UserActivity extends Controller
{
    public function log_activity(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'username' => 'string',
            'status' => 'string',
            'paginate' => 'filled|numeric',
            'page' => 'filled|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        $logs = LogActivity::with(['user']);


        if ($request->filled('username') && $request->username === "visitor") {
            $logs = $logs->whereNull('kullanici_id');
        } else if ($request->filled('username') && $request->username !== "null") {
            $logs = $logs->whereHas('user', function ($q) use ($request) {
                $q->where('kullaniciadi', $request->username);
            });
        }


        if ($request->filled('status') && $request->status !== "null") {
            $logs = $logs->where('status', $request->status);
        }

        $logs = $logs->orderBy('created_at', 'DESC')->paginate($request->paginate, ['*'], 'page', $request->page);

        $users = collect(User::get()->pluck("kullaniciadi")->toArray())->map(function ($data) {
            return [
                "id" => $data,
                "text" => $data
            ];
        })->toArray();
        $users[] = [
            "id" => "null",
            "text" => "Tümü"
        ];
        $users[] = [
            "id" => "visitor",
            "text" => "Ziyaretçi"
        ];
        return response()->json([
            'users' => $users,
            'status' => [
                [
                    "id" => "success",
                    "text" => "Başarılı",
                ],
                [
                    "id" => "fail",
                    "text" => "Hatalı",
                ],
                [
                    "id" => "null",
                    "text" => "Hepsi",
                ]
            ],
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->onEachSide(5)->currentPage(),
                'total' => $logs->total(),
                'total_page' => $logs->total() / $request->paginate,
            ]
        ]);
    }
}
