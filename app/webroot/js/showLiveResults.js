$.ajaxPrefilter( function (options) {
  if (options.crossDomain && jQuery.support.cors && options.type !== 'POST') {
    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
    options.url = `${myBaseUrl}/apis/enableCORS?corsUrl=` + encodeURIComponent(options.url);
    // options.dataType = 'JSON';
    //options.url = "http://cors.corsproxy.io/url=" + options.url;
    // options.data = $.extend(options.data, { corsUrl : options.url });
  }
});

liveScoreTable = $('#liveScoreSheet').DataTable( {
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
    // "order": [[1, 'asc']],
    "searching": false,
    "paging": false,
    "info": false,
  "columnDefs": [{"className": "dt-center", "targets": "_all"}]
});

const generateFplTeamViewLinkByFplIdAndGw = (fplId, gameweek) => {
  return `https://fantasy.premierleague.com/a/team/${fplId}/event/${gameweek}`;
};

function format (data) {
    // `d` is the original data object for the row
    var tableHtml = '<table class="table" style="margin-left:50px;">';
    tableHtml += '<thead><tr><th class="text-center">Name</th><th class="text-center">GW Point</th><th>#Hit</th><th></th>'
    tableHtml += '<th class="text-center">#Hit</th><th class="text-center">GW Point</th><th class="text-center">Name</th></tr></thead>';
    var team1Length = data.entry1Players.length;
    var team2Length = data.entry2Players.length
    $(data.entry1Players).each(function(index,value){
      entry2Players = $(data.entry2Players).eq(index)[0];
      tableHtml+= '<tr class="text-center">';
        tableHtml+=  ('<td><a href="' + generateFplTeamViewLinkByFplIdAndGw(this.player.player_code, currentGameweek) + '">'+this.player.player_name+'</a></td>');
        tableHtml+= ('<td>'+(this.playerPoint)+'</td>');
        tableHtml+= ('<td>(hits: '+ this.hitPoint/4 +')</td>');
        tableHtml+= ('<td ><span style="margin-left:130px;"></span></td>');
        tableHtml+= ('<td>(hits: ' + entry2Players.hitPoint/4 + ')</td>');
        tableHtml+= ('<td>'+(entry2Players.playerPoint)+'</td>');
        tableHtml+= ('<td><a href="' + generateFplTeamViewLinkByFplIdAndGw(entry2Players.player.player_code, currentGameweek) + '">'+entry2Players.player.player_name+'</a></td>');
      tableHtml+= '</tr>';
  });
      

    tableHtml+= '</table>';
    return tableHtml;
    
}

$('.fixtureTable tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = liveScoreTable.row( tr );

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

const getCurrentGw = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: 'https://fantasy.premierleague.com/drf/entry/300023',
      type: "GET",
      crossDomain: true,
      dataType: 'JSON',
      success: function(data, textStatus, jqXHR) {
        console.log(data);
        currentGameweek = data.entry.current_event;
        $("#gameweekShow").text(`Gameweek: ${currentGameweek}`);
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

const getMatchesByGwGroupAndSubgroup = (gameWeek, group_id, subgroup_id) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbMatches/getMatchesByGwGroupAndSubgroup/${gameWeek}/${group_id}/${subgroup_id}`,
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


const getTeamsByGropuAndSubGroup = (group_id, subgroup_id) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbTeams/getTeamsByGropuAndSubGroup/${group_id}/${subgroup_id}`,
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

const getPlayerPicksByFplIdAndGw = (fplId, gameWeek) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `https://fantasy.premierleague.com/drf/entry/${fplId}/event/${gameWeek}/picks`,
      type: 'GET',
      dataType: 'JSON',
      crossDomain: true,
      success: function (data) {
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const getLiveDataFromFplByGw = (gameWeek) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `https://fantasy.premierleague.com/drf/event/${gameWeek}/live`,
      type: 'GET',
      dataType: 'JSON',
      crossDomain: true,
      success: function (data) {
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

let requests = [];
let teamsData = {};

$("#showBtn").on("click", function(){
  const group_id = $("#group_id").val();
  const subgroup_id = $('#subgroup_id').val();
  if(!group_id || !subgroup_id){
    $.alert('Please Select a Group and Sub-group to see the result.');
    return;
  }
  getCurrentGw()
  .then(() => {
    return getTeamsByGropuAndSubGroup(group_id, subgroup_id);
  })
  .then((teams) => {
    teamsData = teams.reduce((accumulator, team) => {
      accumulator[team.FfpbTeam.team_name] = team;
      return accumulator;
    }, {});
    requests = teams.reduce((accumulator, team) => {
      const currentTeamPlayerPicks = team.FfpbPlayer.map((player) => {
        return getPlayerPicksByFplIdAndGw(player.player_code, currentGameweek);
      });
      return accumulator.concat(currentTeamPlayerPicks);
    }, []);
    requests.push(getMatchesByGwGroupAndSubgroup(currentGameweek, group_id, subgroup_id));
    requests.push(getLiveDataFromFplByGw(currentGameweek));
    requests.push(setHitCountByGw(currentGameweek));
    return Promise.all(requests);
  })
  .then((results) => {
    console.log(results);
    const groupMap = { 
      1: '1',
      2: '2',
    }
    results.pop();
    const liveDataFromFpl = results.pop();
    const matches = results.pop();
    // results.splice(results.length - 2, 2);

    const playerPicks = results.reduce((accumulator, playerPick) => {
      accumulator[playerPick.entry_history.entry] = playerPick;
      return accumulator;
    }, {});
    console.log(playerPicks);
    console.log(liveDataFromFpl);
    console.log(matches);
    console.log(teamsData);
    const matchesData = matches.map((match) => {
      const matchData = {
        'plusSign': '<i class="fa fa-plus-circle" aria-hidden="true"></i>'
      };
      matchData.entry1Points = 0;
      matchData.entry2Points = 0;
      matchData.entry1Name = match.entry1.team_name;
      matchData.entry2Name = match.entry2.team_name;
      matchData.subgroupName = groupMap[match.entry1.group_id] + match.entry1.subgroup_id;

      matchData.entry1Players = teamsData[match.entry1.team_name].FfpbPlayer.map((player) => {
        let playerPoint = playerPicks[player.player_code].picks.reduce((accumulator, pick, currentIndex) => {
          if(currentIndex>10) {
            return accumulator+= 0;
          }
          return accumulator+= liveDataFromFpl.elements[pick.element].stats.total_points * pick.multiplier;
        }, 0);

        const hitPoint = playerPicks[player.player_code].entry_history.event_transfers_cost * hitCount;
        playerPoint -= hitPoint;
        matchData.entry1Points+= playerPoint;
        return {
          player,
          playerPoint,
          hitPoint,
        };
      });

      matchData.entry2Players = teamsData[match.entry2.team_name].FfpbPlayer.map((player) => {
        let playerPoint = playerPicks[player.player_code].picks.reduce((accumulator, pick, currentIndex) => {
          if(currentIndex>10) {
            return accumulator+= 0;
          }
          return accumulator+= liveDataFromFpl.elements[pick.element].stats.total_points * pick.multiplier;
        }, 0);

        const hitPoint = playerPicks[player.player_code].entry_history.event_transfers_cost * hitCount;
        playerPoint -= hitPoint
        matchData.entry2Points+= playerPoint;
        return {
          player,
          playerPoint,
          hitPoint,
        };
      });

      return matchData;
    });
  
    console.log(matchesData);
    liveScoreTable.rows.add(matchesData).draw( false );
    liveScoreTable.draw();
  })
  .catch((err) => {
    console.log(err);
  });
});
  
console.log(myBaseUrl);


  