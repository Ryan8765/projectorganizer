<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/tasks';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        /*
        *   function to create a random token - $length is the length of the random token you want generated.
        */
        function rand_token_h($length) {
            $token = bin2hex( openssl_random_pseudo_bytes($length / 2) );  
            return $token;
        }
        //end rand_token


        //activated token for authenticating a user via email after they register.
        $activated_token = rand_token_h( 30 );
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'activated_token' => $activated_token,
            //used to verfiy email.  A value of 1 means a user has verified their email, a value of 0 means they haven't.
            'activated' => 0,
            //set the default sort order
            'sort' => 'custom',
            'password' => bcrypt($data['password']),
        ]);
    }
}
