<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\User;
use App\PasswordReset;

class PasswordResetController extends Controller
{
  /**
   * Create token password reset
   *
   * @param  [string] email
   * @return [string] message
   */
  public function create(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['email' => 'required|string|email']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the user email correctly.'], 200);
    }
  
    $user = User::where('email', $input['email'])->first();
    if (!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }

		$passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => str_random(6)]);

    if ($user && $passwordReset) {
      $user->notify(new PasswordResetRequest($passwordReset->token));
    }

		return response()->json(['success' => true, 'message' => 'We e-mailed to your email address with verfication code. Please input that verification code.'], 200);
  }

  /**
   * Find token password reset
   *
   * @param  [string] $token
   * @return [string] message
   * @return [json] passwordReset object
   */
  public function find(Request $request)
  {
		$input = $request->input();
    $validator = Validator::make($input, ['token' => 'required|string|min:6|max:6']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the correct verification code.'], 200);
    }

    $passwordReset = PasswordReset::where('token', $input['token'])->first();
    if (!$passwordReset) {
      return response()->json(['success' => false, 'message' => 'This password reset token is invalid.'], 200);
    }

    return response()->json(['success' => true, 'message' => ''], 200);
  }

   /**
   * Reset password
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [string] password_confirmation
   * @param  [string] token
   * @return [string] message
   * @return [json] user object
   */
  public function reset(Request $request)
  {
		$input = $request->input();
    $validator = Validator::make($input, ['email' => 'required|string|email']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the email correctly.'], 200);
    }

		$validator = Validator::make($input, ['password' => 'required|string']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the new password.'], 200);
    }

    $validator = Validator::make($input, ['token' => 'required|string|min:6|max:6']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the correct verification code.'], 200);
    }

    $passwordReset = PasswordReset::where([['token', $input['token']], ['email', $input['email']]])->first();
    if (!$passwordReset) {
      return response()->json(['success' => false, 'message' => 'This password reset token is invalid.'], 200);
    }

    $user = User::where('email', $passwordReset->email)->first();
    if (!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }
    $user->password = bcrypt($input['password']);
    $user->save();

    $passwordReset->delete();

    return response()->json(['success' => true, 'message' => ''], 200);
  }
}