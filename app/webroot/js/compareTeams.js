$.ajaxPrefilter( function (options) {
  if (options.crossDomain && jQuery.support.cors) {
    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
    options.url = http + '../apis/enableCORS?corsUrl=' + encodeURIComponent(options.url);
    //options.url = "http://cors.corsproxy.io/url=" + options.url;
  }
});

let currentGameweek;
console.log(jsVars);
const teamsPlayers = JSON.parse(jsVars).teamsPlayers;

const setCurrentGw = () => {
  $.ajax({
    url: 'https://fantasy.premierleague.com/drf/entry/300023',
    type: "GET",
    crossDomain: true,
    dataType: 'JSON',
    success: function(data, textStatus, jqXHR) {
      console.log(data);
      currentGameweek = data.entry.current_event;
      $("#gameweekShow").text(`Gameweek: ${currentGameweek}`);
    },
    error: function(jqXHR, textStatus, errorThrown) {

    }
  });
};

const getPlayerPicksByFplIdAndGw = (fplId, gameWeek) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `https://fantasy.premierleague.com/drf/entry/${fplId}/event/${gameWeek}/picks`,
      type: 'GEt',
      dataType: 'JSON',
      crossDomain: true,
      success: function (data) {
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const getFplPlayersFromFplByGw = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: 'https://fantasy.premierleague.com/drf/elements/',
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

const getEplTeamsFromFplByGw = () => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: 'https://fantasy.premierleague.com/drf/teams/',
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

 console.log($("#compareBtn"));
$("#ajaxLoaderDiv").show();
setCurrentGw();
let eplPlayers;
getFplPlayersFromFplByGw()
  .then((players) => {
    eplPlayers = players;
    console.log(eplPlayers);
    $("#ajaxLoaderDiv").hide();

  });

let eplTeams;
getEplTeamsFromFplByGw()
  .then((teams) => {
    console.log(teams);
    eplTeams = teams;

  });
$("#compareBtn").on('click', function() {
  let requestsToGetFfpbPlayerPicks = [];
  const selectedTeam1 = $("#team1_id").val();
  const selectedTeam2 = $("#team2_id").val();
  const selectedTeam1Name = $("#team1_id option:selected").text();
  const selectedTeam2Name = $("#team2_id option:selected").text();
  console.log(teamsPlayers);

  if(!selectedTeam1 || !selectedTeam2 || (selectedTeam1 === selectedTeam2)) {
    $.alert('Please select 2 team correctly');
    return;
  }

  
  
  requestsToGetFfpbPlayerPicks = requestsToGetFfpbPlayerPicks.concat(
    teamsPlayers[selectedTeam1].map((player) => {
      return getPlayerPicksByFplIdAndGw(player.player_code, currentGameweek)
        .then((r) => {
          return {
            name : "team1PlayerPicks",
            result : r
          };
        });
    })
  );
  requestsToGetFfpbPlayerPicks = requestsToGetFfpbPlayerPicks.concat(
    teamsPlayers[selectedTeam2].map((player) => {
      return getPlayerPicksByFplIdAndGw(player.player_code, currentGameweek)
        .then((r) => {
          return {
            name : "team2PlayerPicks",
            result : r
          };
        });
    })
  );

  const createEplPlayersObjectToCompare = (playersPicks) => {
    let teamwiseEplPlayers = {};
    eplTeams.forEach((team) => { teamwiseEplPlayers[team.name] = {
      opponentTeamName: eplTeams[team.next_event_fixture[0].opponent - 1].name,
      players: {},
      };
    });
    // console.log(teamwiseEplPlayers);
    playersPicks.forEach((playerPicks) => {
      playerPicks.picks.forEach((pick) => {
        const pickedEplPlayer = eplPlayers.find((eplPlayer) => eplPlayer.id === pick.element);
        const eplPlayerName = pickedEplPlayer.first_name + ' ' + pickedEplPlayer.second_name;
        const eplPlayerTeamId = pickedEplPlayer.team - 1;
        // console.log(eplPlayerName);

        if(!teamwiseEplPlayers[eplTeams[eplPlayerTeamId].name].players[eplPlayerName]) {
          teamwiseEplPlayers[eplTeams[eplPlayerTeamId].name].players[eplPlayerName] = { count: 0, captain: 0};
        }
        teamwiseEplPlayers[eplTeams[eplPlayerTeamId].name].players[eplPlayerName].count++;
        if(pick.is_captain){
          teamwiseEplPlayers[eplTeams[eplPlayerTeamId].name].players[eplPlayerName].captain++;
        }
      })
    });

    return teamwiseEplPlayers;
  }

  Promise.all(requestsToGetFfpbPlayerPicks)
    .then((results) => {
      const lookup = results.reduce((prev, curr) => {
        if(!prev[curr.name]) {
          prev[curr.name] = [];
        }
        prev[curr.name].push(curr.result);
        return prev;
      }, {});

      console.log(lookup);
      const team1EplPlayers = createEplPlayersObjectToCompare(lookup['team1PlayerPicks']);
      const team2EplPlayers = createEplPlayersObjectToCompare(lookup['team2PlayerPicks']);
      console.log(team1EplPlayers);
      let tableHtml = '<div class="col-xs-offset-2 col-xs-8" style="margin-top:30px;"><table style="width: -webkit-fill-available;">';
      tableHtml+= '<tr><td><h3>' + selectedTeam1Name + '</h3></td><td><h3>' + selectedTeam2Name + '</h3></td></tr>';
      Object.keys(team1EplPlayers).forEach((teamName) => {
        tableHtml+= '<tr><td colspan="2"><h4>' + teamName + ' vs ' + team1EplPlayers[teamName].opponentTeamName + '</h4></td></tr><tr><td><ul>';
        Object.keys(team1EplPlayers[teamName].players).forEach((playerName) => {
          tableHtml+= '<li>' + playerName + '- ' + team1EplPlayers[teamName].players[playerName].count;
          if(team1EplPlayers[teamName].players[playerName].captain > 0){
            tableHtml+= ' + ' + team1EplPlayers[teamName].players[playerName].captain;
          }
          tableHtml+= '</li>';
        })
        tableHtml+= '</ul></td><td><ul>';
        Object.keys(team2EplPlayers[teamName].players).forEach((playerName) => {
          tableHtml+= '<li>' + playerName + '- ' + team2EplPlayers[teamName].players[playerName].count;
          if(team2EplPlayers[teamName].players[playerName].captain > 0){
            tableHtml+= ' + ' + team2EplPlayers[teamName].players[playerName].captain;
          }
          tableHtml+= '</li>';
        });
        tableHtml+= '</ul></td></tr>';
      });
      tableHtml+= '</table></div>';

      $("#teamComparasionDiv").html(tableHtml);

    })
    .catch((err) => {
      console.log(err);
      $.alert("Something went wrong, please try again.")
    });
      

});  
  

  