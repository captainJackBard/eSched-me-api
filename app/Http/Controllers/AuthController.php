<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Facebook\Facebook;

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

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $user = new \App\User();
        $user->email = $request->input('email');
        $user->password = app('hash')->make($request->input('password'));
        $user->save();
        return response()->json(['message' => 'User Registration Success']);
    }

    public function fblogin(Request $request)
    {
        $fb = new Facebook([
            'app_id' => '273246269726674',
            'app_secret' => '4d37b7b20c749a30c90f9d709c2c4a55',
            'default_graph_version' => 'v2.7',
        ]);

        try {
            $response = $fb->get('/me?fields=id,name', $request['authResponse']['accessToken']);
        }  catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        $user = $response->getGraphUser();

        return response()->json($user->getProperty('first_name'));
    }
}