$.ajaxPrefilter( function (options) {
  if (options.crossDomain && jQuery.support.cors && options.type !== 'POST') {
    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
    options.url = `${myBaseUrl}/apis/enableCORS?corsUrl=` + encodeURIComponent(options.url);
    // options.dataType = 'JSON';
    //options.url = "http://cors.corsproxy.io/url=" + options.url;
    // options.data = $.extend(options.data, { corsUrl : options.url });
  }
});

fixturesTable = $('#fixtureByGw').DataTable( {
    "columns": [
        {
            "className":      'details-control',
            "orderable":      false,
            "data":           "plusSign",
            "defaultContent": ''
        },
        { "data": "entry1Name" },
        { "data": "entry1Points" },
        { "data": "subgroupName" },
        { "data": "entry2Points" },
        { "data": "entry2Name" }
    ],
    "order": [[3, 'asc']],
    "searching": true,
    "paging": false,
    "info": false,
  "columnDefs": [{"className": "dt-center", "targets": "_all"}]
});

function format (data) {
    // `d` is the original data object for the row
    var tableHtml = '<table class="table" style="margin-left:50px;">';
    tableHtml += '<thead><tr><th class="text-center">Name</th><th class="text-center">GW Point</th><th>#Hit</th><th></th>'
    tableHtml += '<th class="text-center">#Hit</th><th class="text-center">GW Point</th><th class="text-center">Name</th></tr></thead>';
    var team1Length = data.entry1Players.length;
    var team2Length = data.entry2Players.length
    $(data.entry1Players).each(function(index, value){
      entry2Player = $(data.entry2Players).eq(index)[0];
      tableHtml+= '<tr class="text-center">';
        tableHtml+=  ('<td><a href="' + this.fplLink + '">'+ this.player_name+'</a></td>');
        tableHtml+= ('<td>'+(this.playerPoint)+'</td>');
        tableHtml+= ('<td>(hits: '+ this.hitPoint +')</td>');
        tableHtml+= ('<td ><span style="margin-left:130px;"></span></td>');
        tableHtml+= ('<td>(hits: ' + entry2Player.hitPoint + ')</td>');
        tableHtml+= ('<td>'+(entry2Player.playerPoint)+'</td>');
        tableHtml+= ('<td><a href="' + entry2Player.fplLink + '">'+entry2Player.player_name+'</a></td>');
      tableHtml+= '</tr>';
  });
      

    tableHtml+= '</table>';
    return tableHtml;
    
}

$('.fixtureTable tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = fixturesTable.row( tr );

    if ( row.child.isShown() ) {
        row.child.hide();
        tr.removeClass('shown');
    }
    else {
        row.child( format(row.data()) ).show();
        tr.addClass('shown');
    }
});

let currentGameweek;
let hitCount = 0;

const setCurrentGw = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: 'https://fantasy.premierleague.com/drf/entry/300023',
      type: "GET",
      crossDomain: true,
      dataType: 'JSON',
      success: function(data, textStatus, jqXHR) {
        console.log(data);
        currentGameweek = data.entry.current_event;
        resolve();
      },
      error: function(jqXHR, textStatus, errorThrown) {

      }
    });
  });
};

const setHitCountByGw = (gameWeek) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbHitCountControlInGws/getHitCountGw/${gameWeek}`,
      type: "GET",
      dataType: 'JSON',
      success: function(data, textStatus, jqXHR) {
        console.log(data)
        hitCount = data[0].FfpbHitCountControlInGw.hitCountControl;
        resolve()
      },
      error: function(jqXHR, textStatus, errorThrown) {

      }
    });
  });
};

const getMatchesByGw = (gameWeek) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbMatches/getMatchesByGw/${gameWeek}`,
      type: 'GET',
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const getAllTeam = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbTeams/getAllTeam`,
      type: 'GET',
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const getPlayersInMatchByMatchId = (matchId) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/ffpbPlayers/getPlayersInMatchByMatchId/${matchId}`,
      type: 'GET',
      dataType: 'JSON',
      success: function (data) {
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const generateFplTeamViewLinkByFplIdAndGw = (fplId, gameweek) => {
  return `https://fantasy.premierleague.com/a/team/${fplId}/event/${gameweek}`;
};

let teamsData = {};
let currenGwMatches = [];

const showFixtureByGw = (gameweek) => {
	getMatchesByGw(gameweek)
	.then((matches) => {
		if(matches.length === 0){
			$.alert(`No fixture is available for gameweek ${gameweek}`);
			return
		}
		currentGameweek = gameweek;
        $("#gameweekShow").text(`Gameweek: ${currentGameweek}`);
		fixturesTable.clear();
		currenGwMatches = matches;

		const playersInMatchesReqs = matches.map((match) => getPlayersInMatchByMatchId(match.FfpbMatch.id));
		const requests = playersInMatchesReqs.concat([setHitCountByGw(currentGameweek)])
		return Promise.all(requests);
	})
	.then((results) => {
		results.pop();
		const palyersInMatches = {};
		results.map((result) => {
			result.map((playerInMatch) => {
				if(!palyersInMatches.hasOwnProperty(playerInMatch.FfpbPlayerInMatch.match_id)){
					palyersInMatches[playerInMatch.FfpbPlayerInMatch.match_id] = {};
				}
				if(!palyersInMatches[playerInMatch.FfpbPlayerInMatch.match_id].hasOwnProperty(playerInMatch.player.team_id)){
					palyersInMatches[playerInMatch.FfpbPlayerInMatch.match_id][playerInMatch.player.team_id] = [];
				}
				palyersInMatches[playerInMatch.FfpbPlayerInMatch.match_id][playerInMatch.player.team_id].push({
					player_name: playerInMatch.player.player_name,
					fplLink: generateFplTeamViewLinkByFplIdAndGw(playerInMatch.player.player_code, currentGameweek),
					hitPoint: playerInMatch.FfpbPlayerInMatch.taken_hit * hitCount,
					playerPoint: playerInMatch.FfpbPlayerInMatch.earned_point,
					usedChip: playerInMatch.FfpbPlayerInMatch.used_chip
				}) 
			})
		});
		const groupMap = { 
			1: '1',
			2: '2',
		}
		const matchesData = currenGwMatches.map((match) => {
		  const matchData = {
		    'plusSign': '<i class="fa fa-plus-circle" aria-hidden="true"></i>'
		  };
		  matchData.entry1Points = match.FfpbMatch.entry1_points;
		  matchData.entry2Points = match.FfpbMatch.entry2_points;
		  matchData.entry1Name = match.entry1.team_name;
		  matchData.entry2Name = match.entry2.team_name;
		  matchData.subgroupName = groupMap[match.entry1.group_id] + match.entry1.subgroup_id;
		  matchData.entry1Players = palyersInMatches[match.FfpbMatch.id][match.FfpbMatch.entry1];
		  matchData.entry2Players = palyersInMatches[match.FfpbMatch.id][match.FfpbMatch.entry2];

		  return matchData;
		});

		console.log(matchesData);
		fixturesTable.rows.add(matchesData).draw( false );
		fixturesTable.draw();
	})
	.catch((err) => {
		console.log(err);
	});
};

let requests = [setCurrentGw(), getAllTeam()];
Promise.all(requests)
	.then((results) => {
		const teams = results.pop();
		teamsData = teams.reduce((accumulator, team) => {
	      accumulator[team.FfpbTeam.team_name] = team;
	      return accumulator;
	    }, {});
		showFixtureByGw(currentGameweek)
	})
	.catch((error) => {
		console.log(error)
	})

$("#prevBtn").on("click", function(){
	showFixtureByGw(currentGameweek - 1);
});

$("#nextBtn").on("click", function(){
	showFixtureByGw(currentGameweek + 1);
});

console.log(myBaseUrl);
