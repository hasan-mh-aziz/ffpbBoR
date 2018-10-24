<?php


App::uses('AppController', 'Controller');


class LeaguesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('League');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */

	public function managers($leagueId = 131514, $gw = 1) {
		$this->autoLayout = false;
		ini_set('max_execution_time', 0); 
		$i = 1;
		do{
			$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-classic-standings/'.$leagueId.'?phase=1&le-page=1&ls-page='.$i;

		// debug($requestedURL);
		
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			foreach($response['standings']['results'] as $value){
				$playerData['name'] = $value['player_name'];
				$playerData['team'] = $value['entry_name'];
				$playerData['gw_point'] = $value['event_total'];
				$playerData['total_point'] = $value['total'];
				$playerData['rank'] = $value['rank'];
				$playerData['team_link'] = 'https://fantasy.premierleague.com/a/team/'.$value['entry'].'/event/'.$gw;

				$allPlayers[] = $playerData;
			}


			// debug($response['standings']);
			$i++;

		}while($response['standings']['has_next'] );

		$gwPoint = [];
		foreach ($allPlayers as $key => $row)
		{
		    $totalPoint[$key] = $row['total_point'];
		}
		array_multisort($totalPoint, SORT_DESC, $allPlayers);
		foreach ($allPlayers as $key => $row)
		{
		    $gwPoint[$key] = $row['gw_point'];
		}
		array_multisort($gwPoint, SORT_DESC, $allPlayers);

		debug($allPlayers);

		$this->set->compact([$allPlayers]);

		// $file = fopen("playerList.csv","w");
		// fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

		// fputcsv($file,['Player Name','Team Name', 'GW Point', 'Toatl Point', 'rank', 'Team Link']);

		// foreach ($allPlayers as $player)
		//   {
		//   fputcsv($file,$player);
		//   }

		// fclose($file);

		// $requestedURL = 'https://fantasy.premierleague.com/drf/leagues-classic-standings/'.$leagueId.'?phase=1&le-page=1&ls-page=2';

		// debug($requestedURL);
		
		// $response = file_get_contents($requestedURL);

		// debug($response);
	}

	public function leagueManagers(){
		$this->autoLayout = false;
		ini_set('max_execution_time', 0); 

	}

	public function getLeagueManagers(){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 

		$leagueId = $_POST['leagueID'];
		$gw = $_POST['gw'];
		$i = 1;
		do{
			$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-classic-standings/'.$leagueId.'?phase=1&le-page=1&ls-page='.$i;

		// debug($requestedURL);
		
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			foreach($response['standings']['results'] as $value){
				$playerData['name'] = $value['player_name'];
				$playerData['team'] = $value['entry_name'];
				$playerData['gw_point'] = $value['event_total'];
				$playerData['total_point'] = $value['total'];
				$playerData['rank'] = $value['rank'];
				$playerData['team_link'] = 'https://fantasy.premierleague.com/a/team/'.$value['entry'].'/event/'.$gw;

				$allPlayers[] = $playerData;
			}


			// debug($response['standings']);
			$i++;

		}while($response['standings']['has_next'] );

		echo json_encode($allPlayers);

	}

	public function h2hResults($playerID = 1780403){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 
		$i = 1;

		$leagueCodes = ['7TEENH2H' => 38267, 'redditH2H' => 514004,];

		// foreach ($leagueCodes as $leagueName => $league) {
		// 	$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-h2h-matches/league/'. $league . '?entry='. $playerID . '&page=1';

		// 	$opts = array(
		// 	  'http'=>array(
		// 	    'method'=>"GET",
		// 	    'header'=>"Accept-language: en-US,en;q=0.8,bn;q=0.6\r\n" .
		// 	              "Cookie: pl_profile=eyJzIjogIld6SXNNamt5T0Rrek5ERmQ6MWJhY2dZOnNJYnNjTUVLSk1NYlpyOXRWeDZ3N2k5a21TOCIsICJ1IjogeyJsbiI6ICJBeml6IiwgImZjIjogMSwgImlkIjogMjkyODkzNDEsICJmbiI6ICJNb2hhbW1lZCBIYXNhbnVsIn19; sessionid='.eJyrVkpPzE2NT85PSVWyUirISSvIUdJRik8sLcmILy1OLYpPSkzOTs1LAUsmVqYW6UEFivUCwHwnqDyKpkyg-mhDHSNLIwtLYxPD2FoAeMAjqg:1be2bv:MCeccL43553yYaF0pc0JQUsa5FA'; _gat=1; _dc_gtm_UA-33785302-1=1; _gat_UA-33785302-1=1; csrftoken=XTj5eUp7pOCWuL4PGTWRX12QKMMzZ0Xu; _ga=GA1.2.1901752270.1471885525; _ga=GA1.3.1901752270.1471885525\r\n"
		// 	  )
		// 	);

		// 	$context = stream_context_create($opts);

		// 	/* Sends an http request to www.example.com
		// 	   with additional headers shown above */
		// 	$fp = fopen($requestedURL, 'r', false, $context);

		// 	$response = file_get_contents($requestedURL,false,$context);
		// 	$response = json_decode($response, true);

		// 	debug($response);
		// }

		do{
			$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-h2h-standings/877812?ls-page='.$i.'&le-page=1&mt-page=1&mn-page=';

		// debug($requestedURL);
		
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			foreach($response['standings']['results'] as $value){
				$playerData['name'] = $value['player_name'];
				$playerData['team'] = $value['entry_name'];
				// $playerData['gw_point'] = $value['event_total'];
				// $playerData['total_point'] = $value['total'];
				// $playerData['rank'] = $value['rank'];
				// $playerData['team_link'] = 'https://fantasy.premierleague.com/a/team/'.$value['entry'].'/event/'.$gw;

				$allPlayers[] = $playerData;
			}


			// debug($response['standings']);
			$i++;

		}while($response['standings']['has_next'] );

		debug($response['standings']);

	}

	public function iccfplResults($playerID = 1780403){
		$this->autoLayout = false;
		ini_set('max_execution_time', 0); 
		$i = 1;



		$leagueCodes = ['7TEENH2H' => 38267, 'redditH2H' => 514004,];
		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from teams where group_id = ' . $groupID;
		$result = $this->League->query($getTeamsDetailsByGroupSql);
		$isCurrentGwEnded = $result[0]['teams']['is_current_gw_ended'];
		$presentCurrentGw = $actualCurrentGw = $result[0]['teams']['current_gameweek'];
		$requestedURL = "https://fantasy.premierleague.com/drf/entry/1780403/event/".$presentCurrentGw."/picks";
		$response = file_get_contents($requestedURL);
		$response = json_decode($response, true);

		// $this->League->updatePointTableByGw(9);
		// debug();
		// exit();
		

		if(!$response['event']['is_current']){
			$gw = $presentCurrentGw;
			do{
				$gw++;
				$requestedURL = "https://fantasy.premierleague.com/drf/entry/1780403/event/".$gw."/picks";
				$response = file_get_contents($requestedURL);
				$response = json_decode($response, true);

			}while(!$response['event']['is_current']);
			$actualCurrentGw = $response['event']['id'];

			$updateCurrentGwSql = 'update teams set current_gameweek = ' . $actualCurrentGw . ', is_current_gw_ended = false';
			$this->League->query($updateCurrentGwSql);
			$isCurrentGwEnded = false;
		}

		// exit();

		if(!$isCurrentGwEnded){
			$requestedURL = "https://fantasy.premierleague.com/drf/event/".$actualCurrentGw."/live";
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			$gwEnded = true;
			foreach ($response['fixtures'] as $key => $fixture) {
				# code...
				if(!$fixture['finished'])
					$gwEnded = false;
			}

			if($gwEnded){
				$this->League->updatePointTableByGw($actualCurrentGw);
				$updateCurrentGwStatusSql = 'update teams set is_current_gw_ended = true';
				$this->League->query($updateCurrentGwStatusSql);

			}
		}
			


		// do{
		// 	$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-h2h-standings/877812?ls-page='.$i.'&le-page=1&mt-page=1&mn-page=';

		// // debug($requestedURL);
		
		// 	$response = file_get_contents($requestedURL);
		// 	$response = json_decode($response, true);

		// 	foreach($response['standings']['results'] as $value){
		// 		$playerData['name'] = $playerName = $value['player_name'];
		// 		$playerData['team'] = $value['entry_name'];
		// 		$playerData['playerCode'] =$playerCode = $value['entry'];
		// 		$allPlayers[] = $playerData;
		// 	}
		// 	$i++;

		// }while($response['standings']['has_next'] );

			

		$this->set(compact('actualCurrentGw'));

		// debug($currentGwMatches);
		// debug($teamData);
		// exit();
		// foreach ($allPlayers as $key => $player) {
		// 	# code...
		// 	$searchPlayerByCodeSql = 'select * from players where player_code = ' . $player['playerCode'];
		// 	$result = $this->League->query($searchPlayerByCodeSql);

		// 	if(empty($result)){
		// 		echo $player['name'] . ' ' . $player['playerCode'] . '<br>';
		// 	}
		// }

		// 
		// debug($teams);

		

		// debug($response['standings']);



	}

	

	public function getPointTable($groupID = 1){
		$this->autoLayout = false;
		$this->autoRender = false;
		$groupID = $_POST['groupId'];

		$getTeamsDetailsByGroupSql = 'select * from teams where group_id = ' . $groupID;
		$result = $this->League->query($getTeamsDetailsByGroupSql);

		echo json_encode($result);
	}

	public function enterFixture($groupID = 1){
		$this->autoLayout = false;
		$this->autoRender = false;
		$getAllTeamSql = 'select * from teams';
		$result = $this->League->query($getAllTeamSql);

		foreach ($result as $key => $value) {
			# code...
			$teams[trim($value['teams']['team_name'])] = $value['teams']['team_id'];
		}

		$fixtureEntry = 'BCC নাগবাবহনী vs Heroes of CCR/ Honours FC vs Knights of Jhenaidah/ Epic Fails of CCC vs শাহী গ্রামবাসী/ Mokras of MCC vs Willian Dollar Babies – RCC/Bosses of BCC vs RCC Puttas/ RCC Lords United vs PCC FC/ Winterfell FC - CCR vs MCC Musketeers';
		$fixtureEntries = explode('/', $fixtureEntry);
		foreach ($fixtureEntries as $key => $match) {
			# code...
			$matchTeams = explode('vs', $match);
			debug(trim($matchTeams[0]));

			$fixtureEntrySql = 'insert into matches (gameweek, entry1, entry2) values ( 9, ' . $teams[trim($matchTeams[0])] . ',' . $teams[trim($matchTeams[1])] . ')';
			$this->League->query($fixtureEntrySql);
		}
	}

	public function enterPlayerFixture(){
		$this->autoLayout = false;
		$this->autoRender = false;
		$getPlayersFixtureSql = 'select * from players left outer join teams on players.team_id = teams.team_id left outer join matches on teams.team_id = entry2';
		$result = $this->League->query($getPlayersFixtureSql);
		// debug($result);
		// exit();

		foreach ($result as $key => $value) {
			# code...
			$playerFixtureEntrySql = 'insert into players_in_match (team_id, player_id, match_id) values ('. $value['teams']['team_id'] . ','  . $value['players']['player_id'] . ',' . $value['matches']['id'] . ')';
			$this->League->query($playerFixtureEntrySql);
		}

		
	}

	public function getLiveMatchPoints($gw = 8){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 
		$i = 1;
		$actualCurrentGw = $gw;

		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from teams where group_id = ' . $groupID;
		$result = $this->League->query($getTeamsDetailsByGroupSql);
		$isCurrentGwEnded = $result[0]['teams']['is_current_gw_ended'];

		$requestedURL = "https://fantasy.premierleague.com/drf/event/".$gw."/live";
		$response = file_get_contents($requestedURL);
		$liveGameData = json_decode($response, true);

		$getMatchesByGwSql = 'select * from matches left outer join teams t1 on t1.team_id = entry1 left outer join teams t2 on t2.team_id = entry2  where gameweek = ' . $actualCurrentGw ;
		$currentGwMatches = $this->League->query($getMatchesByGwSql);
		$allMatchesSet = '';
		foreach ($currentGwMatches as $key => $match) {
			$allMatchesSet.= $match['matches']['id'] . ',';
		}
		$allMatchesSet = rtrim($allMatchesSet);
		
		$getPlayersSql = 'select * from players';
		$players = $this->League->query($getPlayersSql);

		$isCurrentGwEnded = false;

		$teamData = array();

		if(!$isCurrentGwEnded){
			// foreach ($players as $key => $player) {
			// 	# code...
			// 	$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['players']['player_code']."/event/".$actualCurrentGw."/picks";
			// 	$response = file_get_contents($requestedURL);
			// 	$response = json_decode($response, true);

			// 	$playerPoint = 0;
			// 	foreach ($response['picks'] as $key => $value) {
			// 		# code...
			// 		if($value['position'] <= 11){
			// 			$playerPoint+= $liveGameData['elements'][$value['element']]['stats']['total_points'] * $value['multiplier'];
			// 		}
			// 	}

			// 	$playerData =array();
			// 	$playerData['name'] = $player['players']['player_name'];
			// 	$playerData['entry_point'] = $playerPoint;
			// 	$playerData['hit_point'] = $response['entry_history']['event_transfers_cost'];
			// 	$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['players']['player_code'].'/event/'.$actualCurrentGw;

			// 	$teamData[$player['players']['team_id']][] = $playerData;

			// // 	debug($player);
			// // 	debug($response);
			// // exit();

			// }

			echo json_encode($players);
		}
		else{
			$getPlayersFixtureSql = "select * from players left outer join players_in_match on players.player_id = players_in_match.player_id where find_in_set(match_id,'" . $allMatchesSet . "')>0";
			$players = $this->League->query($getPlayersFixtureSql);
			// debug($players);
			// exit();

			foreach ($players as $key => $player) {
				# code...

				$playerData =array();
				$playerData['name'] = $player['players']['player_name'];
				$playerData['entry_point'] = $player['players_in_match']['score'];
				$playerData['hit_point'] = $player['players_in_match']['hit'];
				$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['players']['player_code'].'/event/'.$actualCurrentGw;

				$teamData[$player['players']['team_id']][] = $playerData;

			// 	debug($player);
			// 	debug($response);
			// exit();

			}

		}

		// $liveData['data'] = array();
		// foreach ($currentGwMatches as $key => $match) {
		// 	# code...c
		// 	$tempData['plusSign'] = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
		// 	$tempData['team1Name'] = $match['t1']['team_name'];
		// 	$tempData['team1Id'] = $match['t1']['team_id'];
		// 	$tempData['team2Name'] = $match['t2']['team_name'];
		// 	$tempData['team2Id'] = $match['t2']['team_id'];
		// 	$team1Score = 0;
		// 	foreach ($teamData[$match['t1']['team_id']] as $key => $player) {
		// 		# code...
		// 		$team1Score+= (intval($player['entry_point']) - intval($player['hit_point']));
		// 	}
		// 	if($match['t1']['team_id'] === '1'){
		// 		$tempData['team1Score'] = $team1Score*5/4;
		// 	}
		// 	else
		// 		$tempData['team1Score'] = $team1Score;

		// 	$team2Score = 0;
		// 	foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
		// 		# code...
		// 		$team2Score+= ($player['entry_point'] - $player['hit_point']);
		// 	}
		// 	if($match['t2']['team_id'] === '1'){
		// 		$tempData['team2Score'] = $team2Score*5/4;
		// 	}
		// 	else
		// 		$tempData['team2Score'] = $team2Score;
		// 	$tempData['team1Players'] = $teamData[$match['t1']['team_id']];
		// 	$tempData['team2Players'] = $teamData[$match['t2']['team_id']];

		// 	$tempData['blank'] = "        ";

		// 	$liveData['data'][] = $tempData;
		// }

		// echo json_encode($liveData);
	}

	public function getFixtureByGw($gw = 8){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 
		$i = 1;
		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from teams where group_id = ' . $groupID;
		$result = $this->League->query($getTeamsDetailsByGroupSql);
		$presentCurrentGw = $actualCurrentGw = $result[0]['teams']['current_gameweek'];
		if($presentCurrentGw > $gw){
			$presentCurrentGw = $gw;
		}
		$actualCurrentGw = $gw;

		$getMatchesByGwSql = 'select * from matches left outer join teams t1 on t1.team_id = entry1 left outer join teams t2 on t2.team_id = entry2  where gameweek = ' . $actualCurrentGw ;
		$currentGwMatches = $this->League->query($getMatchesByGwSql);

		$allMatchesSet = '';
		foreach ($currentGwMatches as $key => $match) {
			$allMatchesSet.= $match['matches']['id'] . ',';
		}
		$allMatchesSet = rtrim($allMatchesSet);
		
		$getPlayersFixtureSql = "select * from players left outer join players_in_match on players.player_id = players_in_match.player_id where find_in_set(match_id,'" . $allMatchesSet . "')>0";
		$players = $this->League->query($getPlayersFixtureSql);
		// debug($players);
		// exit();

		$teamData = array();

		foreach ($players as $key => $player) {
			# code...

			$playerData =array();
			$playerData['name'] = $player['players']['player_name'];
			$playerData['entry_point'] = $player['players_in_match']['score'];
			$playerData['hit_point'] = $player['players_in_match']['hit'];
			$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['players']['player_code'].'/event/'.$presentCurrentGw;

			$teamData[$player['players']['team_id']][] = $playerData;

		// 	debug($player);
		// 	debug($response);
		// exit();

		}

		$liveData['data'] = array();
		foreach ($currentGwMatches as $key => $match) {
			# code...c
			$tempData['plusSign'] = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
			$tempData['team1Name'] = $match['t1']['team_name'];
			$tempData['team1Id'] = $match['t1']['team_id'];
			$tempData['team2Name'] = $match['t2']['team_name'];
			$tempData['team2Id'] = $match['t2']['team_id'];
			$team1Score = 0;
			foreach ($teamData[$match['t1']['team_id']] as $key => $player) {
				# code...
				$team1Score+= (intval($player['entry_point']) - intval($player['hit_point']));
			}
			if($match['t1']['team_id'] === 1){
				$tempData['team1Score'] = $team1Score*5/4;
			}
			else
				$tempData['team1Score'] = $team1Score;

			$team2Score = 0;
			foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
				# code...
				$team2Score+= ($player['entry_point'] - $player['hit_point']);
			}
			if($match['t2']['team_id'] === 1){
				$tempData['team2Score'] = $team2Score*5/4;
			}
			else
				$tempData['team2Score'] = $team2Score;
			$tempData['team1Players'] = $teamData[$match['t1']['team_id']];
			$tempData['team2Players'] = $teamData[$match['t2']['team_id']];

			$tempData['blank'] = "        ";

			$liveData['data'][] = $tempData;
		}

		echo json_encode($liveData);
	}

	public function updatePlayerFixture($matchId = null, $gw = 8){
		$this->autoLayout = false;
		$this->autoRender = false;
		$getPlayersFixtureSql = 'select * from players_in_match left outer join players on players_in_match.player_id = players.player_id where match_id =' .$matchId . '';
		$players = $this->League->query($getPlayersFixtureSql);
		// debug($players);
		// exit();
		$actualCurrentGw = 9;

		foreach ($players as $key => $player) {
				# code...

				$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['players']['player_code']."/event/".$actualCurrentGw."/picks";
				$response = file_get_contents($requestedURL);
				$response = json_decode($response, true);

				$playerData =array();
				$playerData['name'] = $player['players']['player_name'];
				$playerData['entry_point'] = $response['entry_history']['points'];
				$playerData['hit_point'] = $response['entry_history']['event_transfers_cost'];
				$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['players']['player_code'].'/event/'.$actualCurrentGw;

				$updatePlayerMatchSql = 'update players_in_match set score = ' . intval($playerData['entry_point']) . ', hit = '. intval($playerData['hit_point']) .' where player_id = ' . $player['players']['player_id'] . ' and match_id = ' . $matchId;
				$this->League->query($updatePlayerMatchSql);

			// 	debug($player);
			// 	debug($response);
			// exit();

			}

		
	}

}
