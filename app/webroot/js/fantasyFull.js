var table = $("#playerListTable").dataTable({
	dom: 'Bfrtip',
  buttons: [
     'csv'
  ],
  "order": [[ 2, "desc" ]]
});

var A = [
  ['Player Name', 'Team Name', 'GW Point', 'Toatl Point', 'rank', 'Team Link']
];

var leagueId = 1120;
var gw = 3;
var currentPage = 1;
var isEnd = false;
var counter = 0;
var leagueName = '';
var tempData;
while (!isEnd) {
  counter++;
  var requestedURL = 'https://fantasy.premierleague.com/drf/leagues-classic-standings/' + leagueId + '?phase=1&le-page=1&ls-page=' + currentPage;

  $.ajax({
    url: requestedURL,
    type: "GET",
    async: false,
    success: function(data, textStatus, jqXHR) {
      //data - response from server
      //console.log(data);
      tempData = data;
      $(data.standings.results).each(function() {
        var managerProfileLink = 'https://fantasy.premierleague.com/a/team/' + this.entry + '/event/' + gw;
        var manager = [this.player_name, this.entry_name, this.event_total, this.total, this.rank, managerProfileLink];
        A.push(manager);
        var tableRow = [this.player_name, this.entry_name, this.event_total, this.total, this.rank, '<a href="'+managerProfileLink+'"></a>'];
        table.row.add( tableRow ).draw( false );
      });

      var hasNext = tempData.standings.has_next;
      //console.log(tempData);
      if (!hasNext) {
        leagueName = tempData.league.name;
        var csvRows = [];
        A.sort(function(x, y) {
          return y[2] - x[2];
        })

        for (var i = 0, l = A.length; i < l; ++i) {
          csvRows.push(A[i].join(','));
        }
        console.log(A);
        var csvString = csvRows.join("%0A");
        var a = document.createElement('a');
        a.href = 'data:attachment/csv;charset=UTF-8,' + csvString;
        a.target = '_blank';
        a.download = leagueName + '.csv';

        document.body.appendChild(a);
        //a.click();
        isEnd = true;
      } else {
        currentPage++;
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {

    }
  });



}
