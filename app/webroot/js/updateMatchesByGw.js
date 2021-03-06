$.ajaxPrefilter( function (options) {
  if (options.crossDomain && jQuery.support.cors && options.type !== 'POST') {
    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
    options.url = http + `${myBaseUrl}/apis/enableCORS?corsUrl=` + encodeURIComponent(options.url);
    // options.dataType = 'JSON';
    //options.url = "http://cors.corsproxy.io/url=" + options.url;
    // options.data = $.extend(options.data, { corsUrl : options.url });
  }
});

const generateFplTeamViewLinkByFplIdAndGw = (fplId, gameweek) => {
  return `https://fantasy.premierleague.com/a/team/${fplId}/event/${gameweek}`;
};

let currentGameweek;
let hitCount = 0;

const setCurrentGw = () => {
  $.ajax({
    url: 'https://fantasy.premierleague.com/drf/entry/300023',
    type: "GET",
    crossDomain: true,
    dataType: 'JSON',
    success: function(data, textStatus, jqXHR) {
      console.log(data);
      currentGameweek = data.entry.current_event;
      $("#updateMatchResultBtn").text(`Update Match Results for Gameweek ${currentGameweek}`);
    },
    error: function(jqXHR, textStatus, errorThrown) {

    }
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
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
        console.log(jqXHR.url);
      },
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

const checkAuthentication = (passcode) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/Apis/checkPasscode`,
      type: 'GET',
      data: {passcode},
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};


const updateTeamsTableByMatchesInfo = (matchesData, passcode) => {
  const updateData = {passcode, matchesData};
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbTeams/updateTeams`,
      type: 'PUT',
      data: updateData,
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const updateMatchesTableByMatchesInfo = (matchesData, passcode) => {
  const updateData = {passcode, matchesData};
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbMatches/updateMatchesByMatchesData`,
      type: 'PUT',
      data: updateData,
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
    });
  });
};

const updatePlayerInMatchesTableByMatchesInfo = (matchesData, passcode) => {
  const updateData = {passcode, matchesData};
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `${myBaseUrl}/FfpbPlayers/updatePlayerInMatchesByMatchesData`,
      type: 'PUT',
      data: updateData,
      dataType: 'JSON',
      success: function (data) {
        console.log(data);
        resolve(data);
    },
      error: function(err) {console.log(err)},
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

const updateMatchResultByGwAndPasscode = (gameWeek, passcode) => {
  let requests = [];
  let teamsData = {};

  $("#ajaxLoaderDiv").show();

  getAllTeam()
    .then((teams) => {
      console.log(teams);
      if(teams[0].FfpbTeam.is_current_gw_ended) {
        throw 'updateAlreadyDone';
      }
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
      requests.push(getMatchesByGw(currentGameweek));
      requests.push(getLiveDataFromFplByGw(currentGameweek));
      requests.push(setHitCountByGw(currentGameweek))
      return Promise.all(requests);
    })
    .then((results) => {
      results.pop();  //pop out result for seeting hit count
      // const excludedPlayerFplCodes = [5705842, 699169, 3360349, 1905001, 5741035, 4986175, 5775441, 1334126, 5711416, 5692478];
      const excludedPlayerFplCodes = varsFromController.excludedPlayerFplCodes;
      let countExcludedPlayer = 0;
      const allowedChips = varsFromController.allowedChips;
      console.log(excludedPlayerFplCodes);
      console.log(allowedChips);
      const groupMap = { 
        1: '1',
        2: '2',
      }
      const liveDataFromFpl = results.pop();
      const matches = results.pop();

      const playerPicks = results.reduce((accumulator, playerPick) => {
        accumulator[playerPick.entry_history.entry] = playerPick;
        return accumulator;
      }, {});
      console.log(playerPicks);
      console.log(liveDataFromFpl);
      console.log(matches);
      console.log(teamsData);
      const matchesData = matches.map((match) => {
        const matchData = {};
        matchData.entry1Points = 0;
        matchData.entry2Points = 0;
        matchData.entry1Id = match.entry1.id;
        matchData.entry2Id = match.entry2.id;
        matchData.matchId = match.FfpbMatch.id;
        matchData.subgroupName = groupMap[match.entry1.group_id] + match.entry1.subgroup_id;

        matchData.entry1Players = teamsData[match.entry1.team_name].FfpbPlayer.map((player) => {
          const activeChip = playerPicks[player.player_code].active_chip;
          let playerPoint = playerPicks[player.player_code].picks.reduce((accumulator, pick, currentIndex) => {
            if(currentIndex>10 && activeChip != 'bboost') {
              return accumulator+= 0;
            }
            return accumulator+= liveDataFromFpl.elements[pick.element].stats.total_points * pick.multiplier;
          }, 0);

          const hitPoint = playerPicks[player.player_code].entry_history.event_transfers_cost * hitCount;
          playerPoint -= hitPoint;
          if(excludedPlayerFplCodes.includes(parseInt(player.player_code, 10)) || (activeChip !== "" && !allowedChips.includes(activeChip))){
            playerPoint = 0;
            countExcludedPlayer++;
          }
          matchData.entry1Points+= playerPoint;
          return {
            player,
            playerPoint,
            hitPoint,
            activeChip,
          };
        });

        matchData.entry2Players = teamsData[match.entry2.team_name].FfpbPlayer.map((player) => {
          const activeChip = playerPicks[player.player_code].active_chip;
          let playerPoint = playerPicks[player.player_code].picks.reduce((accumulator, pick, currentIndex) => {
            if(currentIndex>10 && activeChip != 'bboost') {
              return accumulator+= 0;
            }
            return accumulator+= liveDataFromFpl.elements[pick.element].stats.total_points * pick.multiplier;
          }, 0);

          const hitPoint = playerPicks[player.player_code].entry_history.event_transfers_cost * hitCount;
          playerPoint -= hitPoint
          if(excludedPlayerFplCodes.includes(parseInt(player.player_code, 10)) || (activeChip !== "" && !allowedChips.includes(activeChip))){
            playerPoint = 0;
            countExcludedPlayer++;
          }
          matchData.entry2Points+= playerPoint;
          return {
            player,
            playerPoint,
            hitPoint,
            activeChip,
          };
        });

        return matchData;
      });

      console.log(matchesData);
      console.log(countExcludedPlayer);

      const databaseUpdateRequests = [
        updateMatchesTableByMatchesInfo(matchesData, passcode),
        updatePlayerInMatchesTableByMatchesInfo(matchesData, passcode),
        ];
      return Promise.all(databaseUpdateRequests);
    })
    .then((results) => {
      isRequestRunning = false;
      console.log(results);
      $("#ajaxLoaderDiv").hide();
    })
    .catch((err) => {
      if(err === 'updateAlreadyDone') {
        $.alert(`Sorry update is already done for gameweek ${currentGameweek}`);
      }
      console.log(err);
    });
}

const varsFromController = JSON.parse(jsVars);
if(varsFromController.currentGw > 0) {
  currentGameweek = parseInt(varsFromController.currentGw, 10);
} else{
  setCurrentGw();
}

let givenPasscode;
let isRequestRunning = false;

$("#updateMatchResultBtn").on('click', function(){
  if(isRequestRunning) {
    console.log('running');
    return;
  }
  // isRequestRunning = true;
  // updateMatchResultByGwAndPasscode(currentGameweek, 'amiGroot');
  $.confirm({
      title: 'Please Identify!',
      content: '' +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      '<label>Give your passcode</label>' +
      '<input type="text" placeholder="Your passcode" class="passcode form-control" required />' +
      '</div>' +
      '</form>',
      buttons: {
          formSubmit: {
              text: 'Submit',
              btnClass: 'btn-blue',
              action: function () {
                  givenPasscode = this.$content.find('.passcode').val();
                  checkAuthentication(givenPasscode)
                    .then((hasAuthorization) => {
                      if(hasAuthorization) {
                        isRequestRunning = true;
                        updateMatchResultByGwAndPasscode(currentGameweek, givenPasscode);
                      } else{
                        $.alert("Sorry wrong passcode");
                      }
                    });
              }
          },
          cancel: function () {
              //close
          },
      },
      onContentReady: function () {
          // bind to events
          var jc = this;
          this.$content.find('form').on('submit', function (e) {
              // if the user submits the form by pressing enter in the field.
              e.preventDefault();
              jc.$$formSubmit.trigger('click'); // reference the button and click it
          });
      }
    });
  
});

  
console.log(myBaseUrl);


  