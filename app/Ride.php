<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
  protected $fillable = [
    'user_id', 'distance', 'velocity', 'duration', 'locations'
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function getCreatedAtAttribute($value) {
    return date('Y-m-d', strtotime($value));
  }

  public function getUpdatedAtAttribute($value) {
    return date('Y-m-d', strtotime($value));
  }

	public function getLocationsAttribute($value) {
    return json_decode($value);
  }
}
