
  var datatableGrpA = $("#pointTableA").DataTable({
    "order": [[ 8, "desc" ],[ 7, "desc" ]],
    "columnDefs": [{"className": "dt-center", "targets": "_all"}]
  });

  var datatableGrpB = $("#pointTableB").DataTable({
    "order": [[ 8, "desc" ],[ 7, "desc" ]],
    "columnDefs": [{"className": "dt-center", "targets": "_all"}]
  });


var table = {};
var liveFixtureData = {};

$(document).on('ready', function(){
    var currentPage = 1;
    var isEnd = false;
    var counter = 0;
    var leagueName = '';
    var tempData;
	$.ajax({
	    url: '../Leagues/getPointTable',
	    type: "POST",
	    async :true,
	    dataType: 'json',
	    data: {'groupId' : 1},
	    success: function(data, textStatus, jqXHR) {
	      //data - response from server
	      //console.log(data);
	      tempData = data;
	      $(data).each(function() {
	      	console.log(this);
	        // var managerProfileLink = 'https://fantasy.premierleague.com/a/team/' + this.entry + '/event/' + gw;
	        var pointsEarn = parseInt(this.teams.win*3) + parseInt(this.teams.draw) ;
	        var countDraw = parseInt(this.teams.played) - parseInt(this.teams.win) - parseInt(this.teams.draw);
	        var scoreDifference = parseInt(this.teams.score_for) - parseInt(this.teams.score_against);
	        var tableRow = ['', this.teams.team_name, this.teams.played, this.teams.win, countDraw, this.teams.score_for, this.teams.score_against, scoreDifference, pointsEarn];
	        // console.log(data);
	        datatableGrpA.row.add(tableRow).draw( false );
	      });

	      datatableGrpA.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            cell.innerHTML = i+1;
	        } );
	        datatableGrpA.draw();
	    },
	    error: function(jqXHR, textStatus, errorThrown) {

    	}
  	});
	$.ajax({
	    url: '../Leagues/getPointTable',
	    type: "POST",
	    async :true,
	    dataType: 'json',
	    data: {'groupId' : 2},
	    success: function(data, textStatus, jqXHR) {
	      //data - response from server
	      //console.log(data);
	      tempData = data;
	      $(data).each(function() {
	        // var managerProfileLink = 'https://fantasy.premierleague.com/a/team/' + this.entry + '/event/' + gw;
	        var pointsEarn = parseInt(this.teams.win*3) + parseInt(this.teams.draw);
	        var countDraw = parseInt(this.teams.played) - parseInt(this.teams.win) - parseInt(this.teams.draw);
	        var scoreDifference = parseInt(this.teams.score_for) - parseInt(this.teams.score_against);
	        var tableRow = ['', this.teams.team_name, this.teams.played, this.teams.win, countDraw, this.teams.score_for, this.teams.score_against, scoreDifference, pointsEarn];
	        // console.log(data);
	        datatableGrpB.row.add(tableRow).draw( false );
	      });

	      datatableGrpB.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            cell.innerHTML = i+1;
	        } );
	        datatableGrpB.draw();
	    },
	    error: function(jqXHR, textStatus, errorThrown) {

    	}
  	});

	table['LiveScoreSheet'] = $('#LiveScoreSheet').DataTable( {
        // "ajax": '',
        "data" : liveFixtureData,
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           "plusSign",
                "defaultContent": ''
            },
            { "data": "team1Name" },
            { "data": "team1Score" },
            { "data": "blank" },
            { "data": "team2Score" },
            { "data": "team2Name" }
        ],
        "order": [[1, 'asc']],
    	"columnDefs": [{"className": "dt-center", "targets": "_all"}]
    } );
    table['fixturesTable'] = $('#fixturesTable').DataTable( {
        "ajax": '',
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           "plusSign",
                "defaultContent": ''
            },
            { "data": "team1Name" },
            { "data": "team1Score" },
            { "data": "blank" },
            { "data": "team2Score" },
            { "data": "team2Name" }
        ],
        "order": [[1, 'asc']],
    	"columnDefs": [{"className": "dt-center", "targets": "_all"}]
    } );
});

$("#click").on("click", function(){
	console.log('da');
	$.ajax({
	    url: '../Leagues/getPointTable',
	    type: "POST",
	    dataType: 'json',
	    data: {'groupId' : 1},
	    success: function(data, textStatus, jqXHR) {
	      //data - response from server
	      //console.log(data);
	      // tempData = data;
	      // $(data.standings.results).each(function() {
	      //   var managerProfileLink = 'https://fantasy.premierleague.com/a/team/' + this.entry + '/event/' + gw;
	      //   var tableRow = ['', this.player_name, this.entry_name, this.event_total, this.total, this.rank, '<a href="'+managerProfileLink+'">'+ managerProfileLink +'</a>'];
	      //   console.log(data);
	      //   datatable.row.add(tableRow).draw( false );
	      // });

	      // var hasNext = tempData.standings.has_next;
	      // //console.log(tempData);
	      // if (!hasNext) {
	      //   leagueName = tempData.league.name;
	      //   isEnd = true;
	      //   datatable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	      //       cell.innerHTML = i+1;
	      //   } );
	      //   datatable.draw();
	      // } else {
	      //   currentPage++;
	      // }
	    },
	    error: function(jqXHR, textStatus, errorThrown) {

    	}
  	});
});

/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    var tableHtml = '<table cellpadding="5" cellspacing="0" border="0" style="margin-left:50px;">';
    var team1Length = d.team1Players.length;
    var team2Length = d.team2Players.length
    if(team1Length === team2Length){
    	$(d.team1Players).each(function(index,value){
			console.log($(d.team1Players).eq(index));
			console.log(value);
			tableHtml+= '<tr>';
	        tableHtml+=  ('<td><a href="'+this.link+'">'+this.name+'</a></td>');
	        tableHtml+= ('<td>'+(this.entry_point - this.hit_point)+'</td>');
	        tableHtml+= ('<td>(hit: '+this.hit_point+')</td>');
	        tableHtml+= ('<td ><span style="margin-left:130px;"></span></td>');
	        tableHtml+= ('<td>(hit: '+$(d.team2Players).eq(index)[0].hit_point+')</td>');
	        tableHtml+= ('<td>'+($(d.team2Players).eq(index)[0].entry_point - $(d.team2Players).eq(index)[0].hit_point)+'</td>');
	        tableHtml+= ('<td><a href="'+$(d.team2Players).eq(index)[0].link+'">'+$(d.team2Players).eq(index)[0].name+'</a></td>');
	        tableHtml+= '</tr>';
		});
    }
    else if(team1Length < team2Length){
    	$(d.team1Players).each(function(index,value){
			console.log($(d.team1Players).eq(index));
			console.log(value);
			tableHtml+= '<tr>';
	        tableHtml+=  ('<td><a href="'+this.link+'">'+this.name+'</a></td>');
	        tableHtml+= ('<td>'+(this.entry_point - this.hit_point)+'</td>');
	        tableHtml+= ('<td>(hit: '+this.hit_point+')</td>');
	        tableHtml+= ('<td "><span style="margin-left:130px;"></span></td>');
	        tableHtml+= ('<td>(hit: '+$(d.team2Players).eq(index)[0].hit_point+')</td>');
	        tableHtml+= ('<td>'+($(d.team2Players).eq(index)[0].entry_point - $(d.team2Players).eq(index)[0].hit_point)+'</td>');
	        tableHtml+= ('<td><a href="'+$(d.team2Players).eq(index)[0].link+'">'+$(d.team2Players).eq(index)[0].name+'</a></td>');
	        tableHtml+= '</tr>';
		});

		var lenghtDiff = team2Length - team1Length;
		for(var i = lenghtDiff; i>0; i--){
			tableHtml+= '<tr>';
	        tableHtml+=  ('<td>'+''+'</td>');
	        tableHtml+= ('<td>'+''+'</td>');
	        tableHtml+= ('<td> '+''+'</td>');
	        tableHtml+= ('<td ><span style="margin-left:130px;"></span></td>');
	        tableHtml+= ('<td>(hit: '+$(d.team2Players).eq(team2Length - i)[0].hit_point+')</td>');
	        tableHtml+= ('<td>'+($(d.team2Players).eq(team2Length - i)[0].entry_point - $(d.team2Players).eq(team2Length - i)[0].hit_point)+'</td>');
	        tableHtml+= ('<td><a href="'+$(d.team2Players).eq(team2Length - i)[0].link+'">'+$(d.team2Players).eq(team2Length - i)[0].name+'</a></td>');
	        tableHtml+= '</tr>';
		}
    }
    else{
    	$(d.team2Players).each(function(index,value){
			console.log($(d.team1Players).eq(index));
			console.log(value);
			tableHtml+= '<tr>';
	        tableHtml+= ('<td> <a href="'+$(d.team1Players).eq(index)[0].link+'"'+$(d.team1Players).eq(index)[0].name+'</td>');
	        tableHtml+= ('<td>'+($(d.team1Players).eq(index)[0].entry_point - $(d.team1Players).eq(index)[0].hit_point)+'</td>');
	        tableHtml+= ('<td>(hit: '+$(d.team1Players).eq(index)[0].hit_point+')</td>');
	        tableHtml+= ('<td ><span style="margin-left:130px;"></span></td>');
	        tableHtml+= ('<td>(hit: '+this.hit_point+')</td>');
	        tableHtml+= ('<td>'+(this.entry_point - this.hit_point)+'</td>');
	        tableHtml+=  ('<td><a href="'+this.link+'">'+this.name+'</a></td>');
	        tableHtml+= '</tr>';
		});
		var lenghtDiff = team1Length - team2Length;
		for(var i = lenghtDiff; i>0; i--){
			tableHtml+= '<tr>';
	        tableHtml+= ('<td > <a href="'+$(d.team1Players).eq(team1Length - i)[0].link+'"'+$(d.team1Players).eq(team1Length - i)[0].name+'</td>');
	        tableHtml+= ('<td>'+($(d.team1Players).eq(team1Length - i)[0].entry_point - $(d.team1Players).eq(team1Length - i)[0].hit_point)+'</td>');
	        tableHtml+= ('<td>(hit: '+$(d.team1Players).eq(team1Length - i)[0].hit_point+')</td>');
	        tableHtml+= ('<td><span style="margin-left:130px;"></span></td>');
	        tableHtml+= ('<td> '+''+'</td>');
	        tableHtml+= ('<td>'+''+'</td>');
	        tableHtml+=  ('<td>'+''+'</td>');
	        tableHtml+= '</tr>';
		}
    }
	    

    tableHtml+= '</table>';
    console.log(d.team1Players);
    return tableHtml;
    
}
$("#liveScoreTab").on('click', function() {
	var currentGw = parseInt($('#currentGwLive').text());
	console.log(currentGw);
	var toSetUrl = '../Leagues/getLiveMatchPoints/'+currentGw;

	$.ajax({
	    url: toSetUrl,
	    type: "POST",
	    dataType: 'json',
	    data: {'groupId' : 1},
	    success: function(data, textStatus, jqXHR) {
	      data.forEach(function(currentPlayer){
	      	// console.log(currentPlayer.players.player_code);
      		$.ajax({
			    url: 'https://fantasy.premierleague.com/drf/entry/'+currentPlayer.players.player_code+'/event/' + currentGw + '/picks',
			    type: "GET",
			    // beforeSend: function (response){
	      //           response.setHeader("Access-Control-Allow-Origin","http://clientDomain.com")
			    //    response.setHeader("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,OPTIONS");
			    //    response.setHeader("Access-Control-Allow-Headers", "Content-Type");
	      //       },
	    //         crossDomain: true,
			  //   headers: {
			  //   	"Access-Control-Allow-Origin": "*",
					// "Access-Control-Allow-Headers": "Content-Type "
			  //   },
			    dataType: 'jsonp',
			    success: function(data, textStatus, jqXHR) {
			      console.log(JSON.parse(data));
			    },
			    error: function(jqXHR, textStatus, errorThrown) {

		    	}
		  	});
	      });
	    },
	    error: function(jqXHR, textStatus, errorThrown) {

    	}
  	});

	table['LiveScoreSheet'].ajax.url( toSetUrl ).load();

    
} );


$("#allFixtureTab").on('click', function() {
	var currentFixtureGw = parseInt($('#currentFixtureGw').text());
	console.log(currentFixtureGw);
	var toSetUrl = '../Leagues/getFixtureByGw/'+currentFixtureGw;
	table['fixturesTable'].ajax.url( toSetUrl ).load();
} );

// Add event listener for opening and closing details
$('.scoreTable tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var tableId = $(this).closest('table').attr('id');
    var row = table[tableId].row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        // Open this row
        row.child( format(row.data()) ).show();
        tr.addClass('shown');
    }
} );

$(".changeFixtureButton").on('click', function(){
	console.log(table['fixturesTable']);
	var toBeFixtureGw = parseInt($('#currentFixtureGw').text()) + parseInt($(this).attr('value'));
	$('#currentFixtureGw').text(toBeFixtureGw);
	var toBeUrl = '../Leagues/getFixtureByGw/'+toBeFixtureGw;
	console.log(currentFixtureGw);
	// table['fixturesTable'].ajax.reload();
	table['fixturesTable'].ajax.url( toBeUrl ).load();
	// table['fixturesTable'].draw();
});

  