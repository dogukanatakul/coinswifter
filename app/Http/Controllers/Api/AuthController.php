<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\VerificationEmail;
use App\Jobs\VerificationPhone;
use App\Jobs\WalletCreate;
use App\Models\Bank;
use App\Models\ContractedBank;
use App\Models\Country;
use App\Models\District;
use App\Models\LogActivity;
use App\Models\Province;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketIssue;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserAgreement;
use App\Models\UserBank;
use App\Models\UserContact;
use App\Models\UserKyc;
use App\Models\UserReference;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{

    protected $user;
    protected $referer;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (session()->has('user')) {
                $this->user = session()->get('user');
            } else {
                $this->user = false;
            }
            if (session()->has('referer')) {
                $this->referer = session()->get('referer');
            } else {
                $this->referer = false;
            }
            return $next($request);
        });
    }


    public function signin(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'email' => 'required|filled|email|exists:App\Models\UserContact,value',
            'password' => 'required|filled|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_rules_fail_message')
            ]);
        }
        $request->merge(['password' => pssMngr($request->password)]);
        if (!empty($email = UserContact::where('value', $request->email)->whereIn('status', [0, 1, 3])->orderBy('id', 'DESC')->first())) {
            if (!empty($user = User::where('password', $request->password)->where('id', $email->users_id)->first())) {
                if (empty(LogActivity::where('users_id', $user->id)->where('ip', request()->ip())->where('agent', request()->header('user-agent'))->first()) && Config::get('APP_ENV') === "production") {
                    $user->status = 11;
                    $user->save();
                    User::attempt(['password' => $request->password, 'id' => $email->users_id]);
                    $setUser = (object)[
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name . " " . $user->surname,
                        'status' => 11
                    ];
                } else {
                    $setUser = (object)[
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name . " " . $user->surname,
                        'status' => $user->status
                    ];
                }
                session(['user' => $setUser]);
                return response()->json([
                    'status' => 'success',
                    'user' => $setUser,
                    'message' => __('api_messages.user_signin_success_message')
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => __('api_messages.user_signin_fail_message')
        ]);
    }

    public function signup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'name' => 'required|filled',
            'surname' => 'required|filled',
            'birthday' => 'required|filled|date',
            'nationality' => 'required|filled|numeric',
            'country_code' => 'required|filled|numeric',
            'username' => 'required|string|regex:/(^([a-zA-Z]+)(\d+)?$)/u|unique:App\Models\User,username',
            'password' => 'required|string|min:8|max:50|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/',
            'password_repeat' => 'required|filled|string|same:password',
            'telephone' => 'required|unique:App\Models\UserContact,value',
            'email' => 'required|filled|email|unique:App\Models\UserContact,value',
            'tck_no' => 'exclude_unless:pasaport_no,null|required|numeric|unique:App\Models\User,tck_no',
            'pasaport_no' => 'exclude_unless:tck_no,null|required|unique:App\Models\User,pasaport_no',
            'user_agreement' => 'required|accepted',
            'open_consent' => 'required|accepted',
            'lighting_text' => 'required|accepted',
        ]);
        if ($validator->fails() || (!($request->filled('tck_no') || $request->filled('pasaport_no')))) {
            if (array_key_exists('username', $validator->failed())) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.username_rules_fail_message')
                ]);
            } else if (array_key_exists('email', $validator->failed())) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.email_rules_fail_message')
                ]);
            }
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_rules_fail_message')
            ]);
        } else if (Carbon::parse($request->birthday)->age < 18) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_age_fail_message', ['age' => 18])
            ]);
        }

        if ($request->nationality == 218) {
            $client = new \SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
            try {
                $result = $client->TCKimlikNoDogrula([
                    'TCKimlikNo' => $request->tck_no,
                    'Ad' => $request->name,
                    'Soyad' => $request->surname,
                    'DogumYili' => explode("-", $request->birthday)[0]
                ]);
                if (!$result->TCKimlikNoDogrulaResult) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => "Türkiye vatandaşı kimlik numaralarınız hatalıdır!"
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.system_fail_message')
                ]);
            }
        }
        $request->merge(['password' => pssMngr($request->password)]);
        DB::beginTransaction();
        try {
            $userInsert = User::create([
                'name' => mb_strtotitle_tr($request->name),
                'surname' => mb_strtotitle_tr($request->surname),
                'birthday' => $request->birthday,
                'nationality' => $request->nationality,
                'tck_no' => $request->tck_no,
                'pasaport_no' => $request->pasaport_no,
                'username' => $request->username,
                'password' => $request->password,
                'status' => 0,
            ]);
            WalletCreate::dispatch($userInsert->makeVisible(['id'])->toArray(), 0)->onQueue('createwallet');
            UserContact::create([
                'type' => 'email',
                'value' => $request->email,
                'users_id' => $userInsert->id
            ]);
            UserContact::create([
                'type' => 'telephone',
                'value' => str_replace(" ", "", $request->telephone),
                'users_id' => $userInsert->id,
                'nationality' => $request->country_code
            ]);
            UserAgreement::create([
                'users_id' => $userInsert->id,
                'agreement' => 'user_agreement'
            ]);
            UserAgreement::create([
                'users_id' => $userInsert->id,
                'agreement' => 'open_consent'
            ]);
            UserAgreement::create([
                'users_id' => $userInsert->id,
                'agreement' => 'lighting_text'
            ]);
            if ($this->referer && (!empty($refUser = User::where('referance_code', $this->referer)->first()))) {
                UserReference::create([
                    'users_id' => $refUser->id,
                    'reference_user_id' => $userInsert->id,
                ]);
            }
            DB::commit();
            // Signin
            $setUser = (object)[
                'id' => $userInsert->id,
                'username' => $userInsert->username,
                'name' => $userInsert->name . " " . $userInsert->surname,
                'status' => $userInsert->status
            ];
            session(['user' => $setUser]);
            // Signin Out
            return response()->json([
                'status' => 'success',
                'user' => [
                    'username' => $userInsert->username,
                    'name' => $userInsert->name . " " . $userInsert->surname,
                    'status' => $userInsert->status
                ],
                'message' => __('api_messages.user_create_success_message')
            ]);
        } catch (\Throwable $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }

    public function verificationJob()
    {
        if ($this->user->status === 0 || $this->user->status === 11) {
            $type = "telephone";
        } else if ($this->user->status === 1 || $this->user->status === 12) {
            $type = "email";
        } else if ($this->user->status === 31 || $this->user->status === 32) {
            $checkUpdateContact = UserContact::where('users_id', $this->user->id)->whereIn('status', [3, 4])->first();
            $type = $checkUpdateContact->type;
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        $user = User::with('contacts')->where('username', $this->user->username)->first()->makeVisible('id')->toArray();
        $agent = new Agent();
        $agent->setUserAgent(request()->header('user-agent'));
        if ($type == 'email') {
            if ($this->user->status === 31 || $this->user->status === 32) {
                VerificationEmail::dispatch($user, [
                    'title' => __('email.change_contact'),
                    'description' => __('email.change_contact_description'),
                    'ip' => request()->ip(),
                    'browser' => $agent->browser() ?: '',
                    'platform' => $agent->platform() ?: '',
                    'device' => $agent->device() ?: '',
                    'locked' => true,
                ])->onQueue('verification');
            } else if ($this->user->status === 12) {
                VerificationEmail::dispatch($user, [
                    'title' => __('email.new_session'),
                    'description' => __('email.new_session_description'),
                    'ip' => request()->ip(),
                    'browser' => $agent->browser() ?: '',
                    'platform' => $agent->platform() ?: '',
                    'device' => $agent->device() ?: '',
                    'locked' => true,
                ])->onQueue('verification');
            } else {
                VerificationEmail::dispatch($user, [
                    'title' => __('email.verify_register'),
                    'description' => __('email.verify_register_description'),
                    'ip' => request()->ip(),
                    'browser' => $agent->browser() ?: '',
                    'platform' => $agent->platform() ?: '',
                    'device' => $agent->device() ?: '',
                    'locked' => false,
                ])->onQueue('verification');
            }
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.verification_send_code_email_success_message')
            ]);
        } else if ($type == 'telephone') {
            VerificationPhone::dispatch($user)->onQueue('verification');
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.verification_send_code_telephone_success_message')
            ]);
        }
    }

    public function verificationControl(Request $request, $user = false): \Illuminate\Http\JsonResponse
    {
        if ($user) {
            $this->user = $user;
        }
        $user = User::where('username', $this->user->username)->first();
        $stepConf = [
            0 => [
                'type' => ["telephone"],
                'message' => 'telephone',
                'status' => 1,
                'session_status' => 1,
                'contact_where' => 0,
                'contact_status' => 1,
            ],
            1 => [
                'type' => ["email"],
                'message' => 'email',
                'status' => 2,
                'session_status' => 2,
                'contact_where' => 0,
                'contact_status' => 1,
            ],
            11 => [
                'type' => ["telephone"],
                'message' => 'telephone',
                'status' => 2,
                'session_status' => 12,
                'contact_where' => 1,
                'contact_status' => 1,
            ],
            12 => [
                'type' => ["email"],
                'message' => 'email',
                'status' => 2,
                'session_status' => 2,
                'contact_where' => 1,
                'contact_status' => 1,
            ],
            31 => [
                'type' => ["email", "telephone"],
                'message' => 'change',
                'status' => 32,
                'session_status' => 32,
                'contact_where' => 3,
                'contact_status' => 0,
            ],
            32 => [
                'type' => ["email", "telephone"],
                'message' => 'change',
                'status' => 2,
                'session_status' => 2,
                'contact_where' => 4,
                'contact_status' => 1,
            ],
        ];
        if (!empty(UserVerification::where('code', $request->code)->where('users_id', $user->id)->whereIn('type', $stepConf[$this->user->status]['type'])->orderBy('id', 'DESC')->first())) {
            DB::beginTransaction();
            try {
                UserVerification::where('users_id', $this->user->id)->delete();
                $user->status = $stepConf[$this->user->status]['status'];
                $user->save();
                $userContact = UserContact::where('users_id', $this->user->id)
                    ->whereIn('type', $stepConf[$this->user->status]['type'])
                    ->where('status', $stepConf[$this->user->status]['contact_where']);
                if ($stepConf[$this->user->status]['contact_status'] === 0) {
                    $userContact->delete();
                } else {
                    $userContact->update([
                        'status' => $stepConf[$this->user->status]['contact_status'],
                    ]);
                }
                DB::commit();
            } catch (\Throwable $e) {
                report($e);
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.system_fail_message')
                ]);
            }
            $setUser = (object)[
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name . " " . $user->surname,
                'status' => $stepConf[$this->user->status]['session_status']
            ];
            session(['user' => $setUser]);
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.verification_' . $stepConf[$this->user->status]['message'] . '_success_message')
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.verification_' . $stepConf[$this->user->status]['message'] . '_fail_message')
            ]);
        }
    }

    public function verificationInfo(): \Illuminate\Http\JsonResponse
    {
        $changeContact = false;
        $contact = "";
        $infos = UserContact::where('users_id', $this->user->id)->get();
        if ($this->user->status === 0 || $this->user->status === 11) {
            $changeContact = true;
            $contact = $infos->filter(function ($data) {
                return in_array($data->status, [0, 1]) && $data->type === "telephone";
            })->first();
            $contact = "+" . Country::where('id', $contact->nationality)->first()->phone_code . $contact->value;
        } else if ($this->user->status === 1 || $this->user->status === 12) {
            $changeContact = true;
            $contact = $infos->filter(function ($data) {
                return in_array($data->status, [0, 1]) && $data->type === "email";
            })->first()->value;
        } else if ($this->user->status === 31) {
            $contact = $infos->filter(function ($data) {
                return $data->status == 3;
            })->first();
            if ($contact->type === "telephone") {
                $contact = "+" . Country::where('id', $contact->nationality)->first()->phone_code . $contact->value;
            } else {
                $contact = $contact->value;
            }
        } else if ($this->user->status === 32) {
            $changeContact = true;
            $contact = $infos->filter(function ($data) {
                return $data->status == 4;
            })->first();
            if ($contact->type === "telephone") {
                $contact = "+" . Country::where('id', $contact->nationality)->first()->phone_code . $contact->value;
            } else {
                $contact = $contact->value;
            }
        }

        return response()->json([
            'status' => 'success',
            'contact' => $contact,
            'change' => $changeContact,
        ]);
    }


    public function contractVerify(): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'user_agreement' => 'required|accepted',
            'open_consent' => 'required|accepted',
            'lighting_text' => 'required|accepted',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_rules_fail_message')
            ]);
        }
        DB::beginTransaction();
        try {
            UserAgreement::create([
                'users_id' => $this->user->id,
                'agreement' => 'user_agreement'
            ]);
            UserAgreement::create([
                'users_id' => $this->user->id,
                'agreement' => 'open_consent'
            ]);
            UserAgreement::create([
                'users_id' => $this->user->id,
                'agreement' => 'lighting_text'
            ]);
            DB::commit();
            Cache::forget('conract-' . $this->user->id);
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.user_signin_success_message')
            ]);
        } catch (\Throwable $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        session()->forget('user');
        return response()->json([
            'status' => 'success',
            'message' => __('api_messages.user_logout_success_message')
        ]);
    }

    public function phone_codes(): \Illuminate\Http\JsonResponse
    {
        $phone_codes = Cache::remember('phone_codes-' . date('Y-m-d'), 10, function () {
            return Country::select(DB::raw("CONCAT('+', phone_code) as text"), 'id as value')->get()->toArray();
        });
        return response()->json($phone_codes);
    }

    public function nationalities(): \Illuminate\Http\JsonResponse
    {
        $countries = Cache::remember('countries-' . date('Y-m-d'), 10, function () {
            return Country::select('name as text', 'id as value')->get()->toArray();
        });
        return response()->json($countries);
    }

    public function adress(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'countries_id' => 'nullable|numeric|exists:App\Models\Country,id',
            'provinces_id' => 'nullable|numeric|exists:App\Models\Province,id',
            'districts_id' => 'nullable|numeric|exists:App\Models\District,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'countries' => [],
                'cities' => [],
                'district' => [],
            ]);
        }

        $countries = Cache::remember('countries-' . date('Y-m-d'), 10, function () {
            return Country::select('name as text', 'id as value')->get()->toArray();
        });

        if ($request->filled('countries_id')) {
            $provinces = Cache::remember('provinces-' . date('Y-m-d') . $request->countries_id, 10, function () use ($request) {
                return Province::select('name as text', 'id as value')->where('countries_id', $request->countries_id)->get()->toArray();
            });
        } else {
            $provinces = [];
        }
        if ($request->filled('provinces_id')) {
            $district = Cache::remember('districts-' . date('Y-m-d') . $request->provinces_id, 10, function () use ($request) {
                return District::select('name as text', 'id as value')->where('provinces_id', $request->provinces_id)->get()->toArray();
            });
        } else {
            $district = [];
        }
        $form = UserAddress::select('countries_id', 'provinces_id', 'districts_id', 'address')->where('users_id', $this->user->id)->first();
        return response()->json([
            'countries' => $countries,
            'provinces' => $provinces,
            'district' => $district,
            'form' => (empty($form) ? false : $form->toArray())
        ]);
    }

    public function adressUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'countries_id' => 'required|filled|numeric|exists:App\Models\Country,id',
            'provinces_id' => 'required|filled|numeric|exists:App\Models\Province,id',
            'districts_id' => 'required|filled|numeric|exists:App\Models\District,id',
            'address' => 'required|filled|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        try {
            $request->request->add(['users_id' => $this->user->id]);
            UserAddress::where('users_id', $this->user->id)->delete();
            UserAddress::create($request->toArray());
            Cache::forget('kyc-' . $this->user->id);
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.update_success_message')
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }

    public function forgotFind(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'email' => 'required|filled|email|exists:App\Models\UserContact,value',
            'telephone' => 'required|filled|string|exists:App\Models\UserContact,value',
            'country_code' => 'required|filled|numeric|exists:App\Models\Country,id',
            'birthday' => 'required|filled|date|exists:App\Models\User,birthday',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_forgot_password_fail_message')
            ]);
        }
        $user = User::with('contacts')
            ->where('birthday', $request->birthday)
            ->whereHas('contacts', function ($q) use ($request) {
                $q->where('value', $request->telephone)
                    ->where('nationality', $request->country_code);
            })
            ->whereHas('contacts', function ($q) use ($request) {
                $q->where('value', $request->email);
            })
            ->first()->makeVisible('id');
        if (empty($user)) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.user_forgot_password_fail_message')
            ]);
        } else {
            session(['forgot' => (object)[
                'user' => (object)$user->toArray(),
                'step' => 1
            ]]);
            return response()->json([
                'status' => 'success',
                'info' => [
                    'name_surname' => $user->name[0] . $user->name[1] . ".. " . $user->surname[0] . $user->surname[1] . "...",
                ],
            ]);
        }
    }

    public function forgotVerification(Request $request): \Illuminate\Http\JsonResponse
    {
        if (session()->has('forgot') && ($forgot = session()->get('forgot'))->step > 0) {
            if ($request->filled('code')) {
                if ($forgot->step === 1) {
                    if (!empty($verification = UserVerification::where('code', $request->code)->where('type', 'telephone')->where('users_id', $forgot->user->id)->first())) {
                        $verification->delete();
                        session(['forgot' => (object)[
                            'user' => $forgot->user,
                            'step' => 2
                        ]]);
                        return response()->json([
                            'status' => 'success'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'fail',
                            'message' => __('api_messages.verification_telephone_fail_message')
                        ]);
                    }
                } else if ($forgot->step === 2) {
                    if (!empty($verification = UserVerification::where('code', $request->code)->where('type', 'email')->where('users_id', $forgot->user->id)->first())) {
                        $verification->delete();
                        session(['forgot' => (object)[
                            'user' => $forgot->user,
                            'step' => 3
                        ]]);
                        return response()->json([
                            'status' => 'success'
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'fail',
                            'message' => __('api_messages.verification_email_fail_message')
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.system_fail_message')
                    ]);
                }
            } else {
                if ($forgot->step === 1) {
                    $value = collect($forgot->user->contacts)->filter(function ($q) {
                        return $q['type'] === "telephone";
                    })->first()['value'];
                    VerificationPhone::dispatch((array)$forgot->user)->onQueue('verification');
                    return response()->json([
                        'status' => 'success',
                        'type' => 'telephone_verification',
                        'info' => Str::mask($value, '*', 2, -2),
                        'message' => __('api_messages.verification_send_code_telephone_success_message')
                    ]);
                } else if ($forgot->step === 2) {
                    $value = collect($forgot->user->contacts)->filter(function ($q) {
                        return $q['type'] === "email";
                    })->first()['value'];
                    $agent = new Agent();
                    $agent->setUserAgent(request()->header('user-agent'));
                    VerificationEmail::dispatch((array)$forgot->user, [
                        'title' => __('email.reset_password'),
                        'description' => __('email.reset_password_description'),
                        'ip' => request()->ip(),
                        'browser' => $agent->browser() ?: '',
                        'platform' => $agent->platform() ?: '',
                        'device' => $agent->device() ?: '',
                        'locked' => true,
                    ])->onQueue('verification');
                    return response()->json([
                        'status' => 'success',
                        'type' => 'email_verification',
                        'info' => Str::mask($value, '*', 2, -2),
                        'message' => __('api_messages.verification_send_code_email_success_message')
                    ]);
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.system_fail_message')
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }

    public function forgotChange(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'password' => 'required|filled|string|min:8|max:50|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/',
            'password_confirm' => 'required|filled|string|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        if (session()->has('forgot') && ($forgot = session()->get('forgot'))->step === 3) {
            User::where('id', $forgot->user->id)->update([
                'password' => pssMngr($request->password)
            ]);
            session()->forget('forgot');
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.password_reset_success_message')
            ]);
        } else {
            session()->forget('forgot');
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }


    public function banks(): \Illuminate\Http\JsonResponse
    {
        $userBanks = UserBank::with('bank')->where('users_id', $this->user->id)->get();
        $banks = Bank::select('name as text', 'id as value')->whereNotIn('id', $userBanks->pluck('banks_id')->toArray())->get();
        $systemBanks = ContractedBank::select('banks_id', 'account_name', 'iban', 'account_number', 'branch_code')->with(['bank'])->get();

        $systemBanks = $systemBanks->map(function ($data) {
            $newData = $data->toArray();
            $newData['bank'] = $data->bank->name;
            return $newData;
        });
        return response()->json([
            'status' => 'success',
            'banks' => $banks->toArray(),
            'user_banks' => $userBanks->toArray(),
            'system_banks' => $systemBanks->toArray()
        ]);
    }


    public function bankSet(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'bank' => 'required|filled|integer|exists:App\Models\Bank,id',
            'iban' => 'required|filled|string|unique:App\Models\UserBank,iban',
            'primary' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        } else {
            $primary = $request->primary;
            if ($primary) {
                UserBank::where('users_id', $this->user->id)->update(['primary' => false]);
            } else {
                if (empty(UserBank::where('users_id', $this->user->id)->where('primary', true)->first())) {
                    $primary = true;
                }
            }
            UserBank::updateOrCreate(['iban' => Str::replace(' ', '', $request->iban)], [
                'users_id' => $this->user->id,
                'banks_id' => $request->bank,
                'primary' => $primary,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.bank_set_success_message')
            ]);
        }
    }

    public function bankSetPrimary(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'iban' => 'required|filled|string|exists:App\Models\UserBank,iban'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        } else {
            UserBank::where('users_id', $this->user->id)->update(['primary' => false]);
            UserBank::where('users_id', $this->user->id)
                ->where('iban', $request->iban)
                ->update(['primary' => true]);
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.bank_set_success_message')
            ]);
        }
    }

    public function bankDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'iban' => 'required|filled|string|exists:App\Models\UserBank,iban',
            'primary' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        } else {
            UserBank::where('users_id', $this->user->id)
                ->where('iban', $request->iban)
                ->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.bank_delete_success_message')
            ]);
        }
    }

    public function passwordReset(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'current_password' => 'required|filled|string',
            'new_password' => 'required|filled|string|min:8|max:50|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/',
            'new_password_confirm' => 'required|filled|string|same:new_password',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        if (!empty($checkPass = User::where('id', $this->user->id)->where('password', pssMngr($request->current_password))->first())) {
            $checkPass->sifre = pssMngr($request->new_password);
            $checkPass->save();
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.password_reset_success_message')
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.password_reset_fail_message')
            ]);
        }
    }


    public function referer(): \Illuminate\Http\JsonResponse
    {
        $user = User::find($this->user->id);
        $referers = UserReference::with(['referer_user', 'users_id'])->where('users_id', $user->id)->get();
        $referers = collect($referers)->map(function ($data) {
            return [
                "name" => substr($data->referer_user->name, 0, 1) . "***** " . substr($data->referer_user->surname, 0, 1) . "****",
                "date" => $data->referer_user->created_at->format('Y-m-d'),
            ];
        })->toArray();
        $referers = mb_convert_encoding($referers, 'UTF-8', 'UTF-8');
        return response()->json([
            'status' => 'success',
            'referer' => $user->referance_code,
            'referer_url' => url("?referer=" . $user->referance_code),
            'referers' => $referers,
        ]);
    }

    public function sessions(): \Illuminate\Http\JsonResponse
    {
        $sessions = LogActivity::where('users_id', $this->user->id)->orderBy('id', 'DESC')->get()->groupBy(['ip', 'agent']);
        $sessions = $sessions->mapWithKeys(function ($data) {
            return $data->map(function ($data) {
                $data = $data->first();
                $agent = new Agent();
                $agent->setUserAgent($data->agent);
                return [
                    'ip' => $data->ip,
                    'browser' => $agent->browser() ?: '?',
                    'platform' => $agent->platform() ?: '?',
                    'device' => $agent->device() ?: '?',
                    'last_login' => $data->created_at->format('Y-m-d H:i:s')
                ];
            });
        });
        return response()->json([
            "status" => "success",
            "sessions" => array_values($sessions->toArray()),
        ]);

    }

    public function getContact($responseJson = true): \Illuminate\Http\JsonResponse|array
    {
        $form = UserContact::where('users_id', $this->user->id)
            ->whereIn('status', [0, 1, 4])
            ->get()
            ->groupBy('type');
        $form = $form->mapWithKeys(function ($data, $key) {
            return [$key => [
                'value' => $data->first()->value,
                'country_code' => $data->first()->nationality,
                'status' => $data->first()->status
            ]];
        });
        if ($responseJson) {
            return response()->json([
                "status" => "success",
                "form" => $form->toArray(),
            ]);
        } else {
            return $form->toArray();
        }
    }

    public function updateContact(Request $request): \Illuminate\Http\JsonResponse
    {
        $contact = $this->getContact(false);
        $validator = validator()->make(request()->all(), [
            'telephone.value' => 'required|filled',
            'telephone.country_code' => 'required|filled|integer|exists:App\Models\Country,id',
            'email.value' => 'required|filled|email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_filled_fail_message')
            ]);
        }
        if (empty(UserContact::where('value', $request->telephone['value'])
            ->where('nationality', $request->telephone['country_code'])
            ->where('type', 'telephone')
            ->first())) {
            // telefon değişimi
            if ($contact['telephone']['status'] === 0 || $contact['telephone']['status'] === 4) {
                try {
                    UserContact::where('type', 'telephone')
                        ->where('users_id', $this->user->id)
                        ->whereIn('status', [0, 4])
                        ->update([
                            'value' => $request->telephone['value'],
                            'nationality' => $request->telephone['country_code'],
                        ]);
                    return response()->json([
                        'status' => 'success',
                        'message' => __('api_messages.update_telephone_adress_success_message'),
                        'redirect' => 'telephone_verification'
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.update_telephone_adress_fail_message')
                    ]);
                }
            } else {
                DB::beginTransaction();
                try {
                    $this->user->status = 31;
                    User::where('id', $this->user->id)->update([
                        'status' => 31
                    ]);
                    UserContact::where('type', 'telephone')
                        ->where('users_id', $this->user->id)
                        ->where('status', 1)
                        ->update([
                            'status' => 3,
                        ]);
                    UserContact::create([
                        'users_id' => $this->user->id,
                        'type' => 'telephone',
                        'value' => $request->telephone['value'],
                        'nationality' => $request->telephone['country_code'],
                        'status' => 4
                    ]);
                    session(['user' => $this->user]);
                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => __('api_messages.update_telephone_adress_success_message'),
                        'redirect' => 'telephone_verification'
                    ]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    report($e);
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.system_fail_message'),
                        'code' => ''
                    ]);
                }
            }
        } else if (empty(UserContact::where('value', $request->email['value'])
            ->where('nationality', $request->email['country_code'])
            ->where('type', 'email')
            ->first())) {
            // email değişimi
            if ($contact['email']['status'] === 0 || $contact['email']['status'] === 4) {
                try {
                    UserContact::where('type', 'email')
                        ->where('users_id', $this->user->id)
                        ->whereIn('status', [0, 4])
                        ->update([
                            'value' => trim($request->email['value'])
                        ]);
                    return response()->json([
                        'status' => 'success',
                        'message' => __('api_messages.update_email_adress_success_message'),
                        'redirect' => 'mail_verification'
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.update_email_adress_fail_message')
                    ]);
                }
            } else {
                DB::beginTransaction();
                try {
                    $this->user->status = 31;
                    User::where('id', $this->user->id)->update([
                        'status' => 31
                    ]);
                    UserContact::where('type', 'email')
                        ->where('users_id', $this->user->id)
                        ->where('status', 1)
                        ->update([
                            'status' => 3,
                        ]);
                    UserContact::create([
                        'users_id' => $this->user->id,
                        'type' => 'email',
                        'value' => $request->email['value'],
                        'status' => 4,
                    ]);
                    session(['user' => $this->user]);
                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => __('api_messages.update_email_adress_success_message'),
                        'redirect' => 'mail_verification'
                    ]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    report($e);
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.system_fail_message'),
                        'code' => ''
                    ]);
                }
            }
        }
    }

    public function getKYC(): \Illuminate\Http\JsonResponse
    {
        if (!empty($adress = UserAddress::orderBy('id', 'DESC')->where('users_id', $this->user->id)->first())) {
            $userKyc = UserKyc::whereIn('type', array_keys(kyc_keys()))
                ->where('user_addresses_id', $adress->id)
                ->get();
            $kycs = collect($userKyc)->map(function ($item) {
                return [
                    "file_url" => hiddenImage($item->file_name, $item->file_extension, 'kyc', $item->key),
                    "created_at" => $item->created_at->format('Y-m-d H:i'),
                    "type" => kyc_keys()[$item->type],
                    "status" => ($item->status === 0 ? __('api_messages.waiting') : ($item->status === 1 ? __('api_messages.approved') : __('api_messages.denied'))),
                    'status_message' => $item->explanation
                ];
            })->values();
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.request_kyc_adress_fail_message')
            ]);
        }
        $kyc_select = collect(array_diff_key(kyc_keys(), array_flip($userKyc->pluck('type')->toArray())))->map(function ($text, $value) {
            return [
                "text" => $text,
                "value" => $value,
            ];
        })->values();
        return response()->json([
            'status' => 'success',
            'kyc_select' => $kyc_select,
            'kycs' => $kycs
        ]);
    }

    public function setKYC(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'type' => 'required|filled|string|in:' . implode(",", array_keys(kyc_keys())),
            'file' => 'required|mimes:png,jpg,jpeg|image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        $uid = Uuid::uuid4();
        if (!$extension = strtolower($request->file('file')->getClientOriginalExtension())) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.invalid_file_fail_message')
            ]);
        }
        $fileName = $uid . '.' . $extension;
        if (str_contains($request->file('file')->getContent(), "<?php")) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.invalid_file_fail_message')
            ]);
        }
        try {
            $request->file('file')->storeAs($request->key, $fileName, 'kyc');
            if (!empty($adress = UserAddress::orderBy('id', 'DESC')->where('users_id', $this->user->id)->first())) {
                if (!empty(UserKyc::where('users_id', $this->user->id)->where('user_addresses_id', $adress->id)->where('type', $request->type)->first())) {
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.system_fail_message')
                    ]);
                }
                UserKyc::create([
                    'users_id' => $this->user->id,
                    'user_addresses_id' => $adress->id,
                    'file_name' => $fileName,
                    'file_extension' => $extension,
                    'file_size' => $request->file('file')->getSize(),
                    'type' => $request->type,
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => __('api_messages.request_kyc_success_message')
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.request_kyc_adress_fail_message')
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
    }


    public function user(): \Illuminate\Http\JsonResponse
    {
        $contract = Cache::remember('conract-' . $this->user->id, now()->tz('Europe/Istanbul')->addMinutes(5), function () {
            return UserAgreement::where('users_id', $this->user->id)->get()->count();
        });
        $kyc = Cache::remember('kyc-' . $this->user->id, now()->tz('Europe/Istanbul')->addMinutes(5), function () {
            if (empty($adress = UserAddress::where('users_id', $this->user->id)->first())) {
                return false;
            }
            $kyc = UserKyc::where('users_id', $this->user->id)->whereIn('type', array_keys(kyc_keys()))->where('user_addresses_id', $adress->id)->where('status', 1)->get()->groupBy('type');
            return !(count(array_diff(array_keys(kyc_keys()), array_keys($kyc->toArray()))) > 0);
        });
        if ($this->user->status === 11) {
            $step = "telephone_verification";
        } else if ($this->user->status === 12) {
            $step = "mail_verification";
        } else if ($this->user->status === 0) {
            $step = "telephone_verification";
        } else if ($this->user->status === 1) {
            $step = "mail_verification";
        } else if (($this->user->status === 31 || $this->user->status === 32) && (!empty($checkUpdateContact = UserContact::where('users_id', $this->user->id)->whereIn('status', [3, 4])->first()))) {
            $step = $checkUpdateContact->type === "email" ? "mail_verification" : "telephone_verification";
        } else if ($contract != 3) {
            $step = "contract";
        } else {
            $step = "next";
        }
        return response()->json([
            'status' => 'success',
            'user' => [
                'username' => $this->user->username,
                'name' => $this->user->name,
                'status' => $this->user->status,
                'kyc' => $kyc
            ],
            'step' => $step,
        ]);
    }
    public function getTicket(){
        $ticket = Ticket::with('category','issue')->where('users_id', $this->user->id)->get();
//        $ticket = collect($ticket)->map(function ($data,$key){
//            return [
//                'created_at' => $data->created_at->format('Y-m-d H:i')
//            ];
//        });
        $ticketCategory = TicketCategory::all();
        $ticketIssue = TicketIssue::all();
        return response()->json([
            'status' => 'success',
            'ticket_categories' => $ticketCategory,
            'ticket_issues' => $ticketIssue->toArray(),
            'ticket' => $ticket->toArray()
        ]);
    }

    public function setTicket(Request $request)
    {
//        $validator = validator()->make(request()->all(), [
//            'type' => 'required|filled|string|in:' . implode(",", array_keys(kyc_keys())),
//            'file' => 'required|mimes:png,jpg,jpeg|image|max:2048',
//        ]);
//        if ($validator->fails()) {
//            return response()->json([
//                'status' => 'fail',
//                'message' => __('api_messages.form_parameter_fail_message')
//            ]);
//
//        Ticket::create([
//
//            'users_id' => $request->session()->get('user')->id,
//            'category_id'=>$request->category,
//            'issue_id' => $request->issue,
//            'detail' => $request->detail,
//            'status'=>0,
//        ]);
    }

}
