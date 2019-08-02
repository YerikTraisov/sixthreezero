<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;

use App\User;
use App\Ride;
use App\Exports\RideStatisticsExport;

class DashboardController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
			$this->middleware('auth');
	}

	function getStatistics(Request $request) {
		$input = $request->input();
		$start_date = $request->input('start_date');
		$end_date = $request->input('end_date');

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

		$query = "select u.id, u.name, round(r.distance/1609.34, 4)distance, round(r.duration/3600, 4)duration, round(r.distance/r.duration, 2)velocity 
								from (select user_id, ifnull(sum(distance), 0)distance, ifnull(sum(duration), 0) duration from rides ";

		if(!empty($start_date)) {
			$end_date = isset($end_date) ? $end_date : Carbon::now()->toDateTimeString();
			$start_date = date('Y-m-d 00:00:00', strtotime($start_date));
      $end_date = date('Y-m-d 00:00:00', strtotime($end_date) + 86400);
			$query .= " where '".$start_date."' <= updated_at and updated_at <= '".$end_date."'";
		}
		$query .= " GROUP BY user_id)r
								left join users u on u.id = r.user_id
							where u.id is not null order by duration desc";
		
		return DB::select($query);
	}

	public function index(Request $request)
  	{
		$user = Auth::user();
		$statistics = $this->getStatistics($request);

		$data = compact('user', 'statistics');

		return view('dashboard.index', $data)->withInput($request->all);
	}
	
	public function exportExcel(Request $request) {
		$data = $this->getStatistics($request);
		
		$date = Carbon::now()->toDateTimeString();
		$date = preg_replace('/[\s-]+/', '_', $date);

		return Excel::download(new RideStatisticsExport($data), "statistics_$date.xlsx");
	}

	public function exportCSV(Request $request) {
		$data = $this->getStatistics($request);

		$date = Carbon::now()->toDateTimeString();
		$date = preg_replace('/[\s-]+/', '_', $date);

		return Excel::download(new RideStatisticsExport($data), "statistics_$date.csv");
	}
}
