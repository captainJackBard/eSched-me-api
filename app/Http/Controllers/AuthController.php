<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Facebook\Facebook;
use App\User;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

use Auth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function autoLogin($id)
    {
        $user = User::findOrFail($id);
        $token = $this->jwt->fromUser($user);
        return response()->json(compact('token'));
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {

            $credentials = $request->only('email', 'password');

            if (! $token = $this->jwt->attempt($credentials)) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);

        }

        $user = User::where('email', '=', $request->input('email'))->first();
        if($user->confirmed == 1) return response()->json(compact('token'));

        return response()->json('Please Verify Your Email Address.');
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users,email|email|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'password' => 'required|max:255',
        ]);
        $user = new \App\User();
        $user->email = $request->input('email');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->password = app('hash')->make($request->input('password'));
        $user->confirmed = 0;
        $user->confirmation_code = md5(uniqid(rand(), true));
        if($user->save()) {
            $httpClient = new GuzzleAdapter(new Client());
            $sparky = new SparkPost($httpClient, ['key' => config('sparkpost.client.key')]);
            $sparky->setOptions(['async' => false]);
            
            try {
                $promise = $sparky->transmissions->post([
                    'content' => [
                        'template_id' => 'e-schedme-email-template',
                    ],
                     'recipients' => [
                        [
                            'address' => [
                                'name' => $user->first_name . ' ' . $user->last_name,
                                'email' => $user->email,
                            ],
                            'substitution_data' => [
                                'firstname' => $user->first_name,
                                'lastname' => $user->last_name,
                                'userid' => $user->id,
                                'code' => $user->confirmation_code,
                            ],
                        ],
                    ],

                ]);
                if($promise->getStatusCode() == 200 ) {
                    return response()->json(['message' => 'User Registration Success']);
                }
            }
            catch (\Exception $e) {
                echo $e->getCode()."\n";
                echo $e->getMessage()."\n";
            }
        }
    }

    function random_password( $length = 8 ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

    public function fblogin(Request $request)
    {
        $fb = new Facebook([
            'app_id' => '273246269726674',
            'app_secret' => '4d37b7b20c749a30c90f9d709c2c4a55',
            'default_graph_version' => 'v2.7',
        ]);

        try {
            $response = $fb->get('/me?fields=id,first_name,last_name,email', $request['authResponse']['accessToken']);
        }  catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        
        $user = $response->getGraphUser();

        $first_name = $user->getProperty('first_name');
        $last_name = $user->getProperty('last_name');
        $email = $user->getProperty('email');
        $fuid = $user->getProperty('id');

        $user_info = [
            'first_name' => $user->getProperty('first_name'),
            'last_name' => $user->getProperty('last_name'),
        ];

        $token = "";
        if ($account = User::where('fuid', $fuid)->first()) {
            $token = $this->jwt->fromUser($account);
            // return response()->json(compact('token'));
        } else {
            $account = new User();
            $account->first_name = $first_name;
            $account->last_name = $last_name;
            $account->email = $email;
            $account->fuid = $fuid;
            $account->password = app('hash')->make($this->random_password());
            if($account->save()) {
                try {
                    $token = $this->jwt->fromUser($account);
                } catch (JWTException $e) {
                    return response()->json(['error' => 'could_not_create_token'], 500);
                }
            }
        }

        return response()->json(compact('token'));
    }

}