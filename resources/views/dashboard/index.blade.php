@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/dashboard/index.css">

<?php 
  $start_date = app('request')->input('start_date');
  $end_date = app('request')->input('end_date');
?>

<div class="container">
  <div class="card">
    <!-- page header -->
    <div class="card-header">
      <h2 class="text-center page-title">{{__('Dashboard')}}</h2>
      <div class="btn-group {{app()->getLocale() == 'he' ? 'left' : 'right'}}">
        <a href="{{url('/dashboard/exportExcel')}}" class="btn-action" id="export_excel_file" title="Export Excel File"><img src="{{url('/')}}/img/excel.png" width="40" height="40" download></a>
        <a href="{{url('/dashboard/exportCSV')}}" class="btn-action" id="export_csv_file" title="Export CSV File"><img src="{{url('/')}}/img/csv.png" width="40" height="40" download></a>
      </div>
    </div>

    <!-- page body -->
    <div class="card-body">
      <div class="d-flex justify-content-center align-items-center row">
        <div class='col-md-6'>
          <div class="row">
            <!-- start date -->
            <div class='d-flex col-md-6'>
              <label for="start-date" class="form-label">From:&nbsp;&nbsp;</label>
              <input type="text" name="start-date" id="start_date" class="form-control datepicker" data-date-format="yyyy-mm-dd" data-provide="datepicker" value="{{ isset($start_date) ? $start_date : '' }}">
              <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
              </div>
            </div>
            <!-- end date -->
            <div class='d-flex col-md-6'>
              <label for="end-date" class="form-label">End:&nbsp;&nbsp;</label>
              <input type="text" name="end-date" id="end_date" class="form-control datepicker" data-date-format="yyyy-mm-dd" data-provide="datepicker" value="{{ isset($end_date) ? $end_date : '' }}">
              <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
              </div>
            </div>
          </div>
        </div>
        <div style="display:inline-block;">
          <a href="javascript:void(0);" class="btn-action" id="refresh_statistics" title="Reload"><i class="fa fa-refresh"></i></a>
        </div>
      </div>
      
      <table class="table table-striped" border="1" id="statistics-table">
        <thead style="background-color:#0074D9;color:white;">
          <tr>
            <th data-index='0' data-sort="string" class="sortStyle">#</th>
            <th data-index='1' data-sort="string" class="sortStyle">{{__('User Name')}}</th>
            <th data-index='2' data-sort="string" class="sortStyle">{{__('Average Velocity (mph)')}}</th>
            <th data-index='3' data-sort="string" class="sortStyle">{{__('Duration (h)')}}</th>
            <th data-index='4' data-sort="string" class="sortStyle">{{__('Distance (mile)')}}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($statistics as $stat)
          <tr data-id='{{ $stat->id }}'">
            <td>{{ $loop->index + 1}}</td>
            <td>{{ empty($stat->name) ? "" : $stat->name }}</td>
            <td>{{ empty($stat->velocity) ? "" : $stat->velocity }}</td>
            <td>{{ empty($stat->duration) ? "" : $stat->duration }}</td>
            <td>{{ empty($stat->distance) ? "" : $stat->distance }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <script src="{{url('/')}}/js/dashboard/index.js"></script>
  <script>
    var BASE_URL = "{{ action('DashboardController@index') }}";

    $('#statistics-table').DataTable({
      responsive: true,
      "oLanguage": {
        "oAria": {
        "sSortAscending": "{{__('sorting ascending')}}",
        "sSortDescending": "{{__('sorting descending')}}"
        },
        "oPaginate": {
        "sFirst": "{{__('First')}}",
        "sLast": "{{__('Last')}}",
        "sNext": "{{__('Next')}}",
        "sPrevious": "{{__('Previous')}}"
        },
        "sEmptyTable": "{{__('No data available in table')}}",
        "sLengthMenu": "{{__('show _MENU_ entries')}}",
        "sLoadingRecords": "{{__('Loading...')}}",
        "sProcessing": "{{__('Processing...')}}",
        "sSearch": "{{__('Search')}}",
        "sZeroRecords": "{{__('No matching records found')}}",
        "sInfo": "{{__('Showing _START_ to _END_ of _TOTAL_ entries')}}",
        "sInfoEmpty": "{{__('No entries to show')}}",
        "sInfoFiltered": "{{__('filtered from _MAX_ total records')}}"
      }
    });
  </script>
@stop