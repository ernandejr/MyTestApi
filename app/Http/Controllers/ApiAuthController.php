<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Tymon\JWTAuth\Facades\JWTAuth;

use App\User;

use Validator;

use Mail;


class ApiAuthController extends Controller
{
    protected function create(Request $data)
    {
    	$rules = [
            'name' => 'required|max:255',
            'username' => 'required|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed|regex:^((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]))^',
        ];
    	$validator = Validator::make($data->all(), $rules);
        if($validator->fails())
        {
            $error = $validator->messages();
            return response()->json(compact('error'), 203);
        }
        $data_email = array(
            'name' => $data['name'],
            'validationToken' => md5($data['email'])
            );
        $fromEmail = 'contato@mytest.com';
        $fromName = 'Administração';
        Mail::send('validation', $data_email, function($message) use ($data, $fromName, $fromEmail)
        {
            $message->to($data['email'], $data['name']);
            $message->from($fromEmail, $fromName);
            $message->subject('Confirme sua conta');
        });
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'activetoken' => md5($data['email']),
            'password' => bcrypt($data['password']),
            'active' => 0,
        ]);

        if($user){
        	$credentials = $data->only('email', 'password');
        	$token=null;
        	$token = JWTAuth::attempt($credentials);
        	if($token){
        		return response()->json(compact('token', 'user'));
        	}else{
        		return response()->json(["error" => "Erro ao cadastrar usuário."]);	
        	}
        } 
    }

    public function userAuth(Request $request){
    	$credentials = $request->only('email', 'password');
    	$token=null;

    	try{
    		if(!$token = JWTAuth::attempt($credentials)){
    			return response()->json(["error" => "Usuário e senha invalidos."], 203);
    		}
    	}catch(JWTExeption $ex){
    		return response()->json(["error" => "Algo está errado..."], 500);
    	}

    	return response()->json(compact('token'));

    }

    public function getProfile()
	{
	    try {

	        if (! $user = JWTAuth::parseToken()->authenticate()) {
	            return response()->json(['user_not_found'], 404);
	        }

	    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

	        return response()->json(['token_expired'], $e->getStatusCode());

	    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

	        return response()->json(['token_invalid'], $e->getStatusCode());

	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

	        return response()->json(['token_absent'], $e->getStatusCode());

	    }

	    // the token is valid and we have found the user via the sub claim
	    return response()->json(compact('user'));
	}

	public function logout()
	{
		$result = JWTAuth::invalidate(JWTAuth::getToken());
		if (!$result){
			return response()->json(['error'=> 'Falha ao deslogar'], 403);
		}
			return response()->json(['msg'=> 'Ok']);
	}

    public function validateToken($validationtoken)
    {
        $user = \DB::table('users')->where('activetoken', '=', $validationtoken)->update(array('active'=>1));
        return \Redirect::to('http://locadados.tk/#Profile')->with('message', 'conta ativada com sucesso.');
    }
}
