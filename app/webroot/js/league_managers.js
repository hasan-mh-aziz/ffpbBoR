$(document).on("ready", function(){

  // $(".convert2select2").select2({
  //   placeholder: "Select a League",
  //   allowClear: true
  // });

  var datatable = $("#playerListTable").DataTable({
    dom: 'Bfrtip',
    buttons: [
       'csv'
    ],
    "order": [[ 3, "desc" ]],
    "columnDefs": [{"className": "dt-center", "targets": "_all"}]
  });

  // datatable.on( 'order.dt search.dt', function () {
  //       datatable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
  //           cell.innerHTML = i+1;
  //       } );
  //   } ).draw()

  $(".searchButton").on("click", function(){
    datatable.clear().draw();;
    var leagueId = $(this).prevAll(".leagueIDInput").val();
    var gw = $("#gameWeekInput").val();
    if($.trim(gw) === '')
      gw = 1;
    console.log(gw);
    var currentPage = 1;
    var isEnd = false;
    var counter = 0;
    var leagueName = '';
    var tempData;

      $.ajax({
        url: '../Leagues/getLeagueManagers',
      type: "POST",
      async :true,
      dataType: 'json',
      data: {'leagueID' : leagueId, 'gw' : gw},
        success: function(data, textStatus, jqXHR) {
          //data - response from server
          console.log(data);
          // tempData = data;
          $(data).each(function() {
            var tableRow = ['', this.name, this.team, this.gw_point, this.total_point, this.rank, '<a href="' + this.team_link + '">' + this.team_link +'</a>'];
            console.log(data);
            datatable.row.add(tableRow).draw( false );
          });

          datatable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
          });

          datatable.draw();
        },
        error: function(jqXHR, textStatus, errorThrown) {

        }
      });
  });

});  