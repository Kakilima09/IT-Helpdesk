<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Auth;
use Hash;
use App\Models\Apptitle;
use App\Traits\SocialAuthSettings;
use GuzzleHttp\Client;
use Laravel\Socialite\Facades\Socialite;
use App\Models\SocialAuthSetting;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use App\Models\Seosetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Response;
use GeoIP;
use App\Models\VerifyUser;
use Mail;
use App\Mail\mailmailablesend;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use SocialAuthSettings, ThrottlesLogins, AuthenticatesUsers;

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    public function showSelection()
    {
        $title = Apptitle::first();
        $data['title'] = $title;

        $socialAuthSettings = SocialAuthSetting::first();
        $data['socialAuthSettings'] = $socialAuthSettings;

        $now = now();
        $announcement = announcement::whereDate('enddate', '>=', $now->toDateString())->whereDate('startdate', '<=', $now->toDateString())->get();
        $data['announcement'] = $announcement;

        $announcements = Announcement::whereNotNull('announcementday')->get();
        $data['announcements'] = $announcements;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;
        
        return view('user.ticket-selection')->with($data);
    }

    public function showLoginForm()
    {
        $title = Apptitle::first();
        $data['title'] = $title;

        $socialAuthSettings = SocialAuthSetting::first();
        $data['socialAuthSettings'] = $socialAuthSettings;

        $now = now();
        $announcement = announcement::whereDate('enddate', '>=', $now->toDateString())->whereDate('startdate', '<=', $now->toDateString())->get();
        $data['announcement'] = $announcement;

        $announcements = Announcement::whereNotNull('announcementday')->get();
        $data['announcements'] = $announcements;

        $seopage = Seosetting::first();
        $data['seopage'] = $seopage;

        if(setting('only_social_logins') == 'on'){
            return view('user.auth.onlysociallogin')->with($data);
        }

        return view('user.auth.login')->with($data);
    }

    /**
     * Find user by email or empid
     */
    protected function findUser($login)
    {
        // Cek jika input adalah email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Cari di customer table
            $customer = Customer::where('email', $login)->first();
            if ($customer) {
                return [
                    'type' => 'customer',
                    'user' => $customer,
                    'email' => $customer->email
                ];
            }

            // Cari di user table
            $user = User::where('email', $login)->first();
            if ($user) {
                return [
                    'type' => 'user',
                    'user' => $user,
                    'email' => $user->email
                ];
            }

            return null;
        } else {
            // Input adalah empid, cari di user table
            $user = User::where('empid', $login)->first();
            if ($user) {
                return [
                    'type' => 'user',
                    'user' => $user,
                    'email' => $user->email
                ];
            }

            return null;
        }
    }

    /**
     * Create or get customer from user
     */
    protected function getCustomerFromUser($user)
    {
        // Cari customer berdasarkan email user
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            // Buat customer baru dari data user
            $customer = new Customer();
            $customer->email = $user->email;
            $customer->firstname = $user->first_name ?? $user->name ?? 'User';
            $customer->lastname = $user->last_name ?? '';
            $customer->username = $user->username ?? $user->email;
            $customer->password = $user->password; // Gunakan password yang sama
            $customer->status = 1;
            $customer->verified = 1;
            $customer->userType = 'Employee';
            $customer->save();
        }

        return $customer;
    }

    public function login(Request $request)
    {
        if(setting('login_disable') == 'off')
        {
            // Validasi
            $this->validateLogin($request);

            $login = $request->input('login');
            $password = $request->input('password');

            Log::info('=== CUSTOMER LOGIN ATTEMPT ===', ['login' => $login]);

            // Cari user
            $userData = $this->findUser($login);

            if (!$userData) {
                Log::warning('User not found', ['login' => $login]);
                return back()->withInput()->withErrors(['login' => lang('Invalid email/employee ID or password', 'alerts')]);
            }

            $user = $userData['user'];
            $userType = $userData['type'];
            $email = $userData['email'];

            Log::info('User found', [
                'type' => $userType,
                'user_id' => $user->id,
                'email' => $email
            ]);

            // Cek status
            if ($user->status == 0) {
                Log::warning('Account deactivated', ['user_id' => $user->id]);
                return back()->withInput()->withErrors(['login' => lang('The account has been deactivated.', 'alerts')]);
            }

            // Untuk customer, cek verified
            if ($userType === 'customer' && $user->verified == 0) {
                Log::warning('Email not verified', ['customer_id' => $user->id]);
                return back()->withInput()->withErrors(['login' => lang('Your email has not been verified. Please verify your email.', 'alerts')]);
            }

            // Verifikasi password
            if (!Hash::check($password, $user->password)) {
                Log::warning('Invalid password', ['user_id' => $user->id]);
                return back()->withInput()->withErrors(['login' => lang('Invalid email/employee ID or password', 'alerts')]);
            }

            // Login process
            if ($userType === 'customer') {
                // Login langsung sebagai customer
                Auth::guard('customer')->login($user);
                Log::info('Customer login successful', ['customer_id' => $user->id]);
            } else {
                // Untuk user/employee, buat/get customer dan login
                $customer = $this->getCustomerFromUser($user);
                Auth::guard('customer')->login($customer);
                Log::info('User login as customer successful', [
                    'user_id' => $user->id,
                    'customer_id' => $customer->id
                ]);
            }

            // Update login info
            $cust = Customer::find(Auth::guard('customer')->id());
            $geolocation = GeoIP::getLocation(request()->getClientIp());
            $cust->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $geolocation->ip,
                'last_logins_at' => Carbon::now()->toDateTimeString(),
            ]);

            $request->session()->put('customerticket', Auth::guard('customer')->id());

            Log::info('Login completed successfully', ['customer_id' => Auth::guard('customer')->id()]);

            // ========== UPDATED ==========
            // Redirect ke halaman pemilihan tiket, bukan langsung ke dashboard
            return redirect()->route('user.ticket-selection');
            // =============================

        }
        else
        {
            return back()->withInput()->withErrors(['login' => lang('Technical Issue', 'alerts')]);
        }
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        if(setting('CAPTCHATYPE') == 'off'){
            $rules = [
                'login'     => 'required|max:255',
                'password'  => 'required|min:6|max:255',
            ];
        } else {
            if(setting('CAPTCHATYPE') == 'manual'){
                if(setting('RECAPTCH_ENABLE_LOGIN')=='yes'){
                    $rules = [
                        'login'     => 'required|max:255',
                        'password'  => 'required|min:6|max:255',
                        'captcha' => ['required', 'captcha'],
                    ];
                } else {
                    $rules = [
                        'login'     => 'required|max:255',
                        'password'  => 'required|min:6|max:255',
                    ];
                }
            }
            if(setting('CAPTCHATYPE') == 'google'){
                if(setting('RECAPTCH_ENABLE_LOGIN')=='yes'){
                    $rules = [
                        'login'     => 'required|max:255',
                        'password'  => 'required|min:6|max:255',
                        'g-recaptcha-response' => 'required|captcha',
                    ];
                } else {
                    $rules = [
                        'login'     => 'required|max:255',
                        'password'  => 'required|min:6|max:255',
                    ];
                }
            }
        }

        $this->validate($request, $rules);
    }

    public function ajaxlogin(Request $request)
    {
        if(setting('login_disable') == 'off')
        {
            if(setting('CAPTCHATYPE') == 'off'){
                $validator = Validator::make($request->all(), [
                    'login'     => 'required|max:255',
                    'password'  => 'required|min:6|max:255',
                ]);
            } else {
                if(setting('CAPTCHATYPE') == 'manual'){
                    if(setting('RECAPTCH_ENABLE_LOGIN')=='yes'){
                        $validator = Validator::make($request->all(), [
                            'login'     => 'required|max:255',
                            'password'  => 'required|min:6|max:255',
                            'captcha' => ['required', 'captcha'],
                        ]);
                    } else {
                        $validator = Validator::make($request->all(), [
                            'login'     => 'required|max:255',
                            'password'  => 'required|min:6|max:255',
                        ]);
                    }
                }
                if(setting('CAPTCHATYPE') == 'google'){
                    if(setting('RECAPTCH_ENABLE_LOGIN')=='yes'){
                        $validator = Validator::make($request->all(), [
                            'login'     => 'required|max:255',
                            'password'  => 'required|min:6|max:255',
                            'g-recaptcha-response'  =>  'required',
                        ]);
                    } else {
                        $validator = Validator::make($request->all(), [
                            'login'     => 'required|max:255',
                            'password'  => 'required|min:6|max:255',
                        ]);
                    }
                }
            }

            if ($validator->passes()) {
                $login = $request->login;
                $password = $request->password;

                // Cari user
                $userData = $this->findUser($login);

                if (!$userData) {
                    return response()->json([[3]]); // Not registered
                }

                $user = $userData['user'];
                $userType = $userData['type'];

                // Cek status
                if ($user->status == 0) {
                    return response()->json([[5]]); // Deactivated
                }

                if ($userType === 'customer' && $user->verified == 0) {
                    return response()->json(['login' => $login, 'error' => [4]]); // Not verified
                }

                // Verifikasi password
                if (!Hash::check($password, $user->password)) {
                    return response()->json([[3]]); // Invalid credentials
                }

                // Login process
                if ($userType === 'customer') {
                    Auth::guard('customer')->login($user);
                } else {
                    $customer = $this->getCustomerFromUser($user);
                    Auth::guard('customer')->login($customer);
                }

                // Update login info
                $cust = Customer::find(Auth::guard('customer')->id());
                $cust->update([
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                    'last_login_ip' => $request->getClientIp(),
                    'last_logins_at' => Carbon::now()->toDateTimeString(),
                ]);

                session()->put('customerticket', Auth::guard('customer')->id());

                // ============================================================
                // PERHATIAN: AJAX login tetap mengembalikan [ [1] ].
                // Namun, di view login (JavaScript) Anda HARUS mengubah redirect
                // dari route('client.dashboard') menjadi route('ticket.selection').
                // ============================================================
                return response()->json([[1]]); // Success

            } else {
                return Response::json(['errors' => $validator->errors()]);
            }
        } else {
            return response()->json([[30]]); // Login disabled
        }
    }

    public function ajaxslogin(Request $request)
    {
        $login = $request->login;
        $pass  = $request->password;

        // Cari user
        $userData = $this->findUser($login);

        if (!$userData || $userData['type'] !== 'customer') {
            return response()->json([ [3] ]);
        }

        $customer = $userData['user'];

        if ($customer->status == 0) {
            return response()->json([ [5] ]);
        }

        if ($customer->verified == 0) {
            return response()->json(['login' => $login, 'error' => [4] ]);
        }

        if (Auth::guard('customer')->attempt(['email' => $customer->email, 'password' => $pass])) {
            $cust = Customer::find(Auth::guard('customer')->id());
            $geolocation = GeoIP::getLocation(request()->getClientIp());
            $cust->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $geolocation->ip,
                'last_logins_at' => Carbon::now()->toDateTimeString(),
            ]);
            session()->put('customerticket', Auth::guard('customer')->id());

            // Sama seperti ajaxlogin, kembali [ [1] ] dan ubah JS redirect
            return response()->json([ [1] ]);
        } else {
            return response()->json([ [3] ]);
        }
    }

    public function logout()
    {
        request()->session()->forget('customerticket');
        Auth::guard('customer')->logout();
        if(setting('REGISTER_POPUP') == 'yes'){
            return redirect()->route('home')->with('success',lang('Logout Successfull', 'alerts'));
        }else{
            return back()->with('success',lang('Logout Successfull', 'alerts'));
        }
    }

    // Social Login methods
    public function socialLogin($social)
    {
            $this->setSocailAuthConfigs();
            return Socialite::driver($social)->redirect();
    }

    public function handleProviderCallback($social)
    {
        $this->setSocailAuthConfigs();
        $user = Socialite::driver($social)->user();
        $this->registerOrLogin($user);
        // ========== UPDATED ==========
        // Redirect ke halaman pemilihan tiket setelah social login
        return redirect()->route('user.ticket-selection');
        // =============================
    }

    protected function registerOrLogin($data)
    {
        $user = Customer::where('email', '=', $data->email)->first();
        if(!$user){
            $user = new Customer();

            if(array_key_exists('family_name', $data->user)){
                $user->firstname = $data->user['given_name'];
                $user->lastname = $data->user['family_name'];
                $user->username = $data->name;
            }

            if(array_key_exists('surname', $data->user)){
                $user->firstname = $data->user['firstname'];
                $user->lastname = $data->user['surname'];
                $user->username = $data->nickname;
                $user->logintype = 'envatosociallogin';
            }

            if(!array_key_exists('firstname', $data->user)){
                $user->username = $data->user['name'];
            }

            $user->email = $data->email;
            $user->provider_id = $data->id;
            $user->status = '1';
            $user->verified = '1';
            $user->userType = 'Customer';
            $user->save();
        }

        if($user->logintype == null){
            $user->logintype = 'sociallogin';
            $user->save();
        }
        Auth::guard('customer')->login($user);
    }
}