<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RideStatisticsExport implements FromCollection, WithHeadings
{
  use Exportable;

  public function __construct($data) {
    $this->data = $data;
  }

  public function collection() {
    return collect($this->data)->map(function ($state) {
      return [
        'name'    	=> $state->name,
        'velocity'  => $state->velocity,
        'duration'  => $state->duration,
        'distance'  => $state->distance,
      ];
    });
  }

  public function headings(): array
  {
    return [
      'User Name',
      'Velocity',
      'Duration',
      'Distance',
    ];
  }
}