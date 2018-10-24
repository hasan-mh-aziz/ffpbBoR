$.ajaxPrefilter( function (options) {
  if (options.crossDomain && jQuery.support.cors) {
    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
    options.url = http + '//cors-anywhere.herokuapp.com/' + options.url;
    //options.url = "http://cors.corsproxy.io/url=" + options.url;
  }
});

console.log(JSON.parse(jsVars));
const players = JSON.parse(jsVars).players;

const getPlayerDataFromFplByFplId = (fplId) => {
  return new Promise((resolve, reject) => {
    $.ajax({
        url: `https://fantasy.premierleague.com/drf/entry/${fplId}`,
        type: 'GET',
        crossDomain: true,
        success: function (data) {
          resolve(data);
      },
        error: function(err) {console.log(err)},
    });
  });
}

  const requests = players.map((player) => {
    return getPlayerDataFromFplByFplId(player.FfpbPlayer.player_code);
  });
  console.log(requests);
  Promise.all(requests)
    .then((playersDataFromFpl) => {
      const playerNames = playersDataFromFpl.map((playerFromFpl) => {
        const currentPlayerFromDb = players.find((player) => (parseInt(player.FfpbPlayer.player_code, 10) === parseInt(playerFromFpl.entry.id, 10)) );
        return {
          playerId: currentPlayerFromDb.FfpbPlayer.player_id,
          playerName: `${playerFromFpl.entry.player_first_name} ${playerFromFpl.entry.player_last_name}`,
        };
      });
      console.log(playerNames);
      $.ajax({
        url: `${myBaseUrl}/FfpbPlayers/updatePlayerByAjax`,
        type: 'POST',
        dataType: 'JSON',
        data: {playerNames},
        success: function (data) {
          console.log(data);
      },
        error: function(err) {console.log(err)},
      });
    });
$(document).on("ready", function(){ 
  console.log('requests');
  // $(".searchButton").on("click", function(){
  //   $('#ajaxLoaderDiv').show();
  //   datatable.clear().draw();;
  //   var leagueId = $(this).prevAll(".leagueIDInput").val();
  //   var gw = $("#gameWeekInput").val();
  //   if($.trim(gw) === '')
  //     gw = 1;

  //   const totalPage = findTotalPageOfLeague(leagueId);
  //   var currentPage = 1;
  //   var counter = 0;
  //   var leagueName = '';
  //   var tempData;
  
  // });

});  
  

  