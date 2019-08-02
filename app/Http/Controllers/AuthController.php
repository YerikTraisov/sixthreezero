<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use App\User;

use Avatar;
use Storage;

class AuthController extends Controller
{
  /**
   * Create user
   *
   * @param  [string] name
   * @param  [string] email
   * @param  [string] password
   * @param  [string] password_confirmation
   * @return [string] message
   */
  public function signup(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['bike_id' => 'required|integer']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the bike type correctly.'], 200);
    }

    $validator = Validator::make($input, ['username' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user name correctly.'], 200);
    }

    $validator = Validator::make($input, ['username' => 'unique:users']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'The user who have this user name already exist, please retry with another.'], 200);
    }

    $validator = Validator::make($input, ['name' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the full user name correctly.'], 200);
    }

    $validator = Validator::make($input, ['email' => 'required|string|email']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the email correctly.'], 200);
    }

    $validator = Validator::make($input, ['email' => 'unique:users']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'The user who have this email already exist, please retry with another.'], 200);
    }

    $validator = Validator::make($input, ['password' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input password.'], 200);
    }

    $user = new User([
      'bike_id' => $request->bike_id,
      'username' => $request->username,
      'name' => $request->name,
      'email' => $request->email,
      'password' => bcrypt($request->password),
    ]);

    $access_token = $user->createToken('STZ PAT')->token->id;
    $user['access_token'] = $access_token;
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Successfully created user!',    
      'access_token' => $access_token,        
      'user' => $user
    ], 200);
  }
  
  public function updateBike(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['bike_id' => 'required|integer']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the bike type correctly.'], 200);
    }

		$access_token = $input['access_token'];
		$user = User::where('access_token', $access_token)->first();
		if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
		}

    $user['bike_id'] = $input['bike_id'];
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Successfully updated user!',    
      'user' => $user
    ], 200);
  }

  public function updateName(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['name' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user name correctly.'], 200);
    }

    $access_token = $input['access_token'];
		$user = User::where('access_token', $access_token)->first();
		if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
		}

    $user['name'] = $request->name;
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Successfully updated user!',    
      'user' => $user
    ], 200);
  }

  public function updatePassword(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['old_password' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the old user password correctly.'], 200);
    }

    $validator = Validator::make($input, ['password' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user password correctly.'], 200);
    }

		$access_token = $input['access_token'];
		$user = User::where('access_token', $access_token)->first();
		if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
		}

    $user['password'] = bcrypt($request->password);
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Successfully updated user!',    
      'user' => $user
    ], 200);
  }

  public function updateEmail(Request $request)
  {
    $input = $request->input();
		$access_token = $input['access_token'];
		$user = User::where('access_token', $access_token)->first();
		if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
		}  

    $validator = Validator::make($input, ['email' => 'unique:users,email,'.$user->id]);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'The user who have this email already exist, please retry with another.'], 200);
    }

    $validator = Validator::make($input, ['email' => 'required|string|email']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user email correctly.'], 200);
    }

    $user['email'] = $request->email;
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'Successfully updated user!',
      'user' => $user
    ], 200);
  }

  /**
   * Login user and create token
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [boolean] remember_me
   * @return [string] access_token
   * @return [string] token_type
   * @return [string] expires_at
   */
  public function login(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['username' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user name correctly.'], 200);
    }

    $validator = Validator::make($input, ['password' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user password correctly.'], 200);
    }

    $username = $request->input('username');
    $password = $request->input('password');
    
    $user = User::where('username', $username)->orWhere('email', $username)->first();
    if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }

    $credentials['username'] = $user['username'];
    $credentials['password'] = $password;
    if(!Auth::attempt($credentials)) {
      return response()->json(['success' => false, 'message' => 'Unauthorized'], 200);
    }

    if(empty($user['access_token'])) {
      $access_token = $user->createToken('STZ PAT')->token->id;
      $user['access_token'] = $access_token;
      $user->save();
    }

    return response()->json([
      'success' => true,
      'message' => '',
      'access_token' => $user['access_token'],
      'user' => $user
    ], 200);
  }
 }