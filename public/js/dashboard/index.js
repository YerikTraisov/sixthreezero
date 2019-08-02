$(function () {
  $('.datepicker').datepicker();

  $('#refresh_statistics').click(function() {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    
    var query_strings = [];
    if(typeof start_date != 'undefined' && start_date != '') {
      query_strings.push("start_date="+start_date);
    }
    if(typeof end_date != 'undefined' && end_date != '') {
      query_strings.push("end_date="+end_date);
    }

    var new_location = BASE_URL
    if(query_strings.length > 0) {
      new_location += "?" + query_strings.join('&&');
    }
    location.href = new_location;
  });
});
