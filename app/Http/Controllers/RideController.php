<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use App\User;
use App\Ride;

class RideController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function get(Request $request)
  {
    $input = $request->input();
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    $user = User::where('access_token', $input['access_token'])->first();
    if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }

    if(!empty($start_date)) {
      $validator = Validator::make($input, ['start_date' => 'date']);
      if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => 'Please input the start date correctly.'], 200);
      }
    }

    if(!empty($end_date)) {
      $validator = Validator::make($input, ['end_date' => 'date']);
      if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => 'Please input the end date correctly.'], 200);
      }
    }

    if(empty($start_date)) {
      $rides = Ride::where('user_id', $user->id)->get();
    } else {
      $end_date = isset($end_date) ? $end_date : Carbon::now()->toDateTimeString();
      $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
      $end_date = date('Y-m-d 00:00:00', strtotime($end_date) + 86400);
      $rides = Ride::where('user_id', $user->id)->whereBetween('updated_at', [$start_date, $end_date])->get();
    }

    return response()->json([
      'success' => true,
      'message' => '',
      'rides' => $rides
    ], 200);
  }

  /**
   * create a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['distance' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the distance correctly.'], 200);
    }

    $validator = Validator::make($input, ['velocity' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the velocity correctly.'], 200);
    }

    $validator = Validator::make($input, ['duration' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the duration correctly.'], 200);
    }

    $validator = Validator::make($input, ['locations' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the locations correctly.'], 200);
    }

    $user = User::where('access_token', $input['access_token'])->first();
    if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }

    $ride = Ride::create([
      'user_id' => $user->id,
      'distance' => $input['distance'],
      'velocity' => $input['velocity'],
      'duration' => $input['duration'],
      'locations' => $input['locations']
    ]);

    return response()->json([
      'success' => true, 
      'message' => 'Ride is successfully created!',
      'ride' => $ride
    ], 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $input = $request->input();
    $validator = Validator::make($input, ['id' => 'required|integer']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the ride id correctly.'], 200);
    }

    $validator = Validator::make($input, ['distance' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the distance correctly.'], 200);
    }

    $validator = Validator::make($input, ['velocity' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the velocity correctly.'], 200);
    }

    $validator = Validator::make($input, ['duration' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the duration correctly.'], 200);
    }

    $validator = Validator::make($input, ['locations' => 'required']);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'message' => 'Please input the locations correctly.'], 200);
    }

    $user = User::where('access_token', $input['access_token'])->first();
    if(!$user) {
      return response()->json(['success' => false, 'message' => 'Sorry, but we can not find this user.'], 200);
    }
    
    $ride = Ride::find($input['id']);
    if(!$ride) {
      return response()->json(['success' => false, 'message' => 'Sorry, but can not find this ride data.'], 200);
    }

    Ride::where('id', $input['id'])->update([
      'distance' => $input['distance'], 'velocity' => $input['velocity'],
      'duration' => $input['duration'], 'locations' => $input['locations']
    ]);

    $ride = Ride::find($input['id']);
    return response()->json([
      'success' => true, 
      'message' => 'Ride is successfully updated!',
      'ride' => $ride
    ], 200);
  }

  public function statistics(Request $request) {
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    if(isset($start_date)) {
      $end_date = isset($end_date) ? $end_date : Carbon::now()->toDateTimeString();            
      $rides = Ride::whereBetween('updated_at', [$start_date, $end_date])->get();
    } else {
      $rides = Ride::all();
    }
  }
}
