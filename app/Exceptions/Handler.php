<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $err = [
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
            \App\Helpers\LogActivity::addToLog(['status_text' => implode(" | ", $err), 'status' => 'fail']);
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $err = [
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
            \App\Helpers\LogActivity::addToLog(['status_text' => implode(" | ", $err), 'status' => 'fail']);
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ], 200);
        }
        return parent::render($request, $e); // TODO: Change the autogenerated stub
    }

}
