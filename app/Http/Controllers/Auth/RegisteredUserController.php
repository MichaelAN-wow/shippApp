<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
            'company_name' => 'nullable|string|max:255',
        ]);
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        
        $captchaResponse = $request->input('cf-turnstile-response');
        $secretKey = env('CLOUDFLARE_SECRET_KEY');
        if (!$captchaResponse) {
            return redirect()->back()->with(['error' => 'CAPTCHA verification failed: No token received.']);
        }

        $verifyResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey,
            'response' => $captchaResponse,
        ]);

        $result = $verifyResponse->json();

        if (!isset($result['success']) || !$result['success']) {
            return redirect()->back()->with(['error' => 'CAPTCHA verification failed.']);
        }

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Email already exists!');
        }

        $token = base64_encode($request->email . '##' . $request->name . '##' . $request->company_name . '##' . strtotime('+1 minutes', time()));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activation_token' => $token,
        ]);

        $mail_data = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => 'Activate your account',
            'activate_email_link' => url('https://onhandsolution.com/activate-account/' . $token)
        ];
        try {
            Mail::send('emails.activation', $mail_data, function ($message) use ($mail_data) {
                // $message->from(get_setting('mail_from_address'), get_setting('mail_from_name'));
                $message->to($mail_data['email'], $mail_data['name']);
                $message->subject($mail_data['subject']);
            });
          
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        event(new Registered($user));

        // Redirect to login page with a success message
        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }
}
