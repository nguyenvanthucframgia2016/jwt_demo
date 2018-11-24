<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserToken;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TokenController extends Controller
{
    public function generate(Request $request)
    {
        $username = $request->input('username');

        try {
        	$user = UserToken::where('username', '=', $username)->first();

        	if (!$user) {
        		$user = UserToken::create([
        			'username' => $username
        		]);
        	}

        	// verify the credentials and create a token for the user
	        if (! $token = JWTAuth::fromUser($user)) { 
	            return response()->json(['error' => 'invalid_credentials'], 401);
	        }

	        $user->token = $token;
	        $user->save();
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }
}
