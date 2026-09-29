<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Auth;
use Exception;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
           //
        });
    }
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        if (strpos($exception->getMessage(), "Access denied for user 'forge'") !== false) {
            try {
                $info = [
                    'time'             => date('Y-m-d H:i:s'),
                    'sapi'             => PHP_SAPI,
                    'php'              => PHP_VERSION,
                    'user_agent'       => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'request_method'   => $_SERVER['REQUEST_METHOD'] ?? null,
                    'request_uri'      => $_SERVER['REQUEST_URI'] ?? null,
                    'argv'             => isset($_SERVER['argv']) ? json_encode($_SERVER['argv']) : null,
                    'docroot'          => $_SERVER['DOCUMENT_ROOT'] ?? null,
                    'app_base'         => base_path(),
                    'env_db_user'      => env('DB_USERNAME'),
                    'env_db_host'      => env('DB_HOST'),
                    'getenv_db_user'   => getenv('DB_USERNAME'),
                    'server_db_user'   => $_SERVER['DB_USERNAME'] ?? null,
                    'server_app_env'   => $_SERVER['APP_ENV'] ?? null,
                    'getenv_app_env'   => getenv('APP_ENV'),
                    'config_db_user'   => config('database.connections.mysql.username'),
                    'env_file_exists'  => file_exists(base_path('.env')) ? 'yes' : 'no',
                    'env_file_size'    => file_exists(base_path('.env')) ? filesize(base_path('.env')) : -1,
                    'session_id'       => session_id(),
                ];
                @file_put_contents(
                    storage_path('logs/forge-debug.log'),
                    json_encode($info).PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );
            } catch (\Throwable $ignored) {
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Exception && $exception->getMessage() === 'processing data') {
            return redirect()->route('admin.testinginfo');
        }
        if ($exception instanceof \Exception && $exception->getMessage() === 'error response') {
            return new Response('');
        }
        return parent::render($request, $exception);
    }


    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson())
        {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if ($request->is('customer') || $request->is('customer/*'))
        {
            return redirect()->guest('/customer/login');
        }
        if ($request->is('admin') || $request->is('admin/*'))
        {
            return redirect()->guest('/admin/login');
        }

        return redirect()->guest(route('login'));
    }
}
