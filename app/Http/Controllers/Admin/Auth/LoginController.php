<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Apptitle;
use App\Models\SocialAuthSetting;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Validation\ValidationException;
use App\Models\Seosetting;
use Auth;
use Session;
use GeoIP;
use App\Models\Announcement;
use App\Helper\Installer\trait\ApichecktraitHelper;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use ThrottlesLogins, AuthenticatesUsers {
        logout as performLogout;
    }

    use ApichecktraitHelper;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    public function showloginform()
    {
        $title = Apptitle::first();
        $data['title'] = $title;

        $socialAuthSettings = SocialAuthSetting::first();
        $data['socialAuthSettings'] = $socialAuthSettings;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        $now = now();
        $announcement = announcement::whereDate('enddate', '>=', $now->toDateString())->whereDate('startdate', '<=', $now->toDateString())->get();
        $data['announcement'] = $announcement;

        $announcements = Announcement::whereNotNull('announcementday')->get();
        $data['announcements'] = $announcements;

        return view('admin.auth.login', ['url' => 'admin/login'])->with($data);
    }

    protected function credentials(Request $request)
    {
        $login = $request->input('login');

        // Debug logging
        Log::info('Login attempt', [
            'input' => $login,
            'is_email' => filter_var($login, FILTER_VALIDATE_EMAIL),
            'field' => filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'empid'
        ]);

        // Determine if the input is email or empid
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'empid';

        return [
            $field => $login,
            'password' => $request->password,
            'status' => '1'
        ];
    }

    public function redirectTo()
    {
        $user = auth()->user();
        if ($user) {
            return 'admin/';
        }
    }

    protected function validateLogin(Request $request)
    {
        if (setting('CAPTCHATYPE') == 'off') {
            $rules = [
                'login' => 'required|string|max:255',
                'password' => 'required|string|max:255'
            ];
        } else {
            if (setting('CAPTCHATYPE') == 'manual') {
                if (setting('RECAPTCH_ENABLE_ADMIN_LOGIN') == 'yes') {
                    $rules = [
                        'login' => 'required|string|max:255',
                        'password' => 'required|string|max:255',
                        'captcha' => ['required', 'captcha'],
                    ];
                } else {
                    $rules = [
                        'login' => 'required|string|max:255',
                        'password' => 'required|string|max:255'
                    ];
                }
            }
            if (setting('CAPTCHATYPE') == 'google') {
                if (setting('RECAPTCH_ENABLE_ADMIN_LOGIN') == 'yes') {
                    $rules = [
                        'login' => 'required|string|max:255',
                        'password' => 'required|string|max:255',
                        'g-recaptcha-response'  =>  'required|recaptcha',
                    ];
                } else {
                    $rules = [
                        'login' => 'required|string|max:255',
                        'password' => 'required|string|max:255'
                    ];
                }
            }
        }

        $this->validate($request, $rules);
    }

    public function userInactiveMessage()
    {
        throw ValidationException::withMessages([
            'login' => ['error' => lang('Your Account is Inactive. Please Contact to Admin.', 'alerts')],
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check user existence and status
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'empid';

        $user = User::where($field, $login)->first();

        // Debug logging
        Log::info('User lookup', [
            'input' => $login,
            'field' => $field,
            'user_found' => $user ? $user->id : 'null',
            'user_status' => $user ? $user->status : 'null',
            'user_verified' => $user ? $user->verified : 'null'
        ]);

        if ($user && $user->status == '0') {
            return $this->userInactiveMessage();
        }

        if ($user && $user->verified == '0') {
            return $this->userverifiedMessage();
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Debug failed login
        Log::warning('Login failed', [
            'input' => $login,
            'field' => $field,
            'credentials' => $this->credentials($request)
        ]);

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        // Debug credentials
        Log::info('Attempt login with credentials', $credentials);

        $attempt = $this->guard()->attempt(
            $credentials,
            $request->filled('remember')
        );

        Log::info('Login attempt result', ['success' => $attempt]);

        return $attempt;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'login' => [trans('auth.failed')],
        ]);
    }

    public function validatelog()
    {
        $sessionPath = storage_path('framework/sessions');
        $sessionFiles = glob($sessionPath . '/*');
        foreach ($sessionFiles as $sessionFile) {
            unlink($sessionFile);
        }
        Session::flush();

        return redirect()->route('login');
    }

    public function userverifiedMessage()
    {
        throw ValidationException::withMessages([
            'login' => ['error' => lang('Your Account is Not Verified.', 'alerts')],
        ]);
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect()->route('login');
    }
}
