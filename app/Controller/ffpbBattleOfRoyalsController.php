<?php


App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel');
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel'.DS.'IOFactory.php'));


class ffpbBattleOfRoyalsController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('ffpbBattleOfRoyal');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */

	
	public function collectTeamData() {
		$this->autoLayout = false;
		//$this->autoRender = false;

		$this->ffpbBattleOfRoyal->updateTeamDataFromFile('files/teamData.csv');

	}

	public function setGroupOfTeam() {
		$this->autoLayout = false;
		//$this->autoRender = false;

		$teams = $this->ffpbBattleOfRoyal->getAllTeams();
		$getTeamNameFromTeams = function($team) {
		    return $team['ffpb_teams']['team_name'];
		};

    	$teamNames = array_map($getTeamNameFromTeams, $teams);

    	$this->set(compact('teamNames'));

	}

	public function setupGame() {
		$this->autoLayout = false;
		$this->autoRender = false;
		$leagueId = 910796;
		$gw = 11;
		ini_set('max_execution_time', 0); 
		$i = 1;
		$counter = 0;
		do{
			$requestedURL = 'https://fantasy.premierleague.com/drf/leagues-classic-standings/'.$leagueId.'?phase=1&le-page='.$i.'&ls-page=1';

		// debug($requestedURL);
		
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			foreach($response['new_entries']['results'] as $value){

				$playerData['name'] = (strtolower($value['player_first_name']. ' ' . $value['player_last_name'])); 
				$playerData['code'] = $value['entry'];
				$playerData['team_link'] = 'https://fantasy.premierleague.com/a/team/'.$value['entry'].'/event/'.$gw;
				$teamData[(strtolower($value['entry_name']))][$playerData['name']] = $playerData;
				$counter++;
				// $allPlayers[] = $teamData;
			}


			// debug($response['standings']);
			$i++;

		}while($response['new_entries']['has_next'] );

		// foreach ($variable as $key => $value) {
		// 	# code...
		// }
		ksort($teamData);
		debug($teamData);
		// print_r($teamData) ;

		$getPlayersTeamSql = 'select * from ffpb_players left outer join ffpb_teams on ffpb_players.team_id = ffpb_teams.team_id';
		$result = $this->ffpbBattleOfRoyal->query($getPlayersTeamSql);

		$remainingTeamData = $teamData;
		

		foreach ($result as $key => $player) {
			# codea...
			$actualPlayerData['name'] = $playerName = trim(strtolower($player['ffpb_players']['player_name']),' ');
			$actualPlayerData['fb_id'] = $player['ffpb_players']['fb_id'];
			$playerTeamName = trim(strtolower($player['ffpb_teams']['team_name']));
			if(isset($teamData[$playerTeamName][$playerName])){
				$actualPlayerData['team_link'] = $teamData[$playerTeamName][$playerName]['team_link'];

				// $updatePlayerQuery = 'update ffpb_players set fpl_link = "' . $teamData[$playerTeamName][$playerName]['team_link'] . '" where player_id = ' . $player['ffpb_players']['player_id'];
				// $this->ffpbBattleOfRoyal->query($updatePlayerQuery);
				unset($remainingTeamData[$playerTeamName][$playerName]);
			}
			else{
				$actualPlayerData['team_link'] = 'absent';
				// debug($playerName);
				// debug($player);
				// debug($playerTeamName . ' ' . $remainingTeamData[$playerTeamName][$playerName]);
				// debug($playerTeamName . ' ' . $teamData[$playerTeamName][$playerName]);
			}
			
			$actualTeamData[$player['ffpb_teams']['team_name']][] = $actualPlayerData;
		}
		ksort($actualTeamData);
		// foreach ($actualTeamData as $nameTEam => $team) {
		// 	# code...
		// 	foreach ($team as $key => $player) {
		// 		# code...
		// 		if($player['team_link'] === 'absent'){
		// 			debug($nameTEam . '=>' );
		// 			debug($team);
		// 		}
		// 	}
		// }
		ksort($remainingTeamData);
		debug($remainingTeamData);

		// require_once('/path/to/PHPExcel.php');

		// $excelFile = "Battle-of-Royals-Final-Draft.xlsx";

		// $pathInfo = pathinfo($excelFile);
		// $type = $pathInfo['extension'] == 'xlsx' ? 'Excel2007' : 'Excel5';

		// $objReader = PHPExcel_IOFactory::createReader($type);
		// $objPHPExcel = $objReader->load($excelFile);

		// foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		//     $worksheets[] = $worksheet->toArray();
		// }

		// // debug($worksheets);

		// foreach ($worksheets[0] as $key => $value) {
		// 	# code...
		// 	if($value[0] != null){
		// 		$subGroupName = $value[0];
		// 	}
		// 	if($value[1] != null){
		// 		$teamName = $value[1];
		// 	}
		// 	$playerData['name'] = $value[2]; 
		// 	$playerData['fb_id'] = $value[3]; 

		// 	$groupData[$subGroupName][$teamName][] = $playerData;
		// }

		// // debug($groupData);
		// $subGroupCount = 0;
		// $teamCount = 0;
		// $groupNames = ['A', 'B'];

		// $idName = 'id';
		// $tableName = 'ffpb_groups';
		// $lastInsertIdSql = 'select max('.$idName. ') from ' . $tableName;
		// foreach ($groupData as $key => $value) {
		// 	# code...
		// 	if($subGroupCount %8 === 0){
		// 		$groupCount = intval($subGroupCount/8);
		// 		$groupInsertSql = 'insert into ffpb_groups(group_name) values("'.$groupNames[$groupCount].'")';
		// 		$this->ffpbBattleOfRoyal->query($groupInsertSql);
		// 		$idName = 'id';
		// 		$tableName = 'ffpb_groups';
		// 		$lastInsertIdSql = 'select max('.$idName. ') from ' . $tableName;
		// 		$insertedGroupId = $this->ffpbBattleOfRoyal->query($lastInsertIdSql);
		// 		$insertedGroupId = $insertedGroupId[0][0]['max('.$idName. ')'];
		// 		$insertedGroupId = $groupCount;
		// 	}
		// 	$subGroupInsertSql = 'insert into ffpb_subgroups(subgroup_name, group_id) values("'. $key .'",'. $insertedGroupId .')';
		// 	$this->ffpbBattleOfRoyal->query($subGroupInsertSql);
		// 	$idName = 'id';
		// 	$tableName = 'ffpb_subgroups';
		// 	$lastInsertIdSql = 'select max('.$idName. ') from ' . $tableName;
		// 	$insertedSubGroupId = $this->ffpbBattleOfRoyal->query($lastInsertIdSql);
		// 	$insertedSubGroupId = $insertedSubGroupId[0][0]['max('.$idName. ')'];
		// 	$insertedSubGroupId = ++$subGroupCount;
		// 	// debug($lastInsertId());
		// 	foreach ($value as $key => $team) {
		// 		# code...
		// 		$teamInsertSql = 'insert into ffpb_teams(team_name, subgroup_id) values("'. $key .'",'. $insertedSubGroupId .')';
		// 		$this->ffpbBattleOfRoyal->query($teamInsertSql);
		// 		$idName = 'team_id';
		// 		$tableName = 'ffpb_teams';
		// 		$lastInsertIdSql = 'select max('.$idName. ') from ' . $tableName;
		// 		$insertedteamId = $this->ffpbBattleOfRoyal->query($lastInsertIdSql);
		// 		$insertedteamId = $insertedteamId[0][0]['max('.$idName. ')'];
		// 		$insertedteamId = ++$teamCount;
		// 	debug($this->ffpbBattleOfRoyal->query('select * from ffpb_teams'));
		// 		foreach ($team as $key => $player) {
		// 			# code...
		// 			$playerInsertSql = 'insert into ffpb_players(player_name, team_id, fb_id) values("'. $player['name'] .'",'. $insertedteamId . ',"'. $player['fb_id'] .'")';
		// 			$this->ffpbBattleOfRoyal->query($playerInsertSql);
		// 		}
		// 	}

		// 	debug($this->ffpbBattleOfRoyal->query('select * from ffpb_teams'));
		// 	// exit();	
		// }
		// debug($groupData);

		// debug($response);
	}

	public function setFixtures(){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 

		$getTeamsSql = 'select * from ffpb_teams';
		$ffpb_teams = $this->ffpbBattleOfRoyal->query($getTeamsSql);

		

		foreach ($ffpb_teams as $key => $team) {
			# code...
			$subgroupData[$team['ffpb_teams']['subgroup_id']][] = $team['ffpb_teams'];
		}
		debug($subgroupData);

		$subGroupFixtureChecker = [];
		$serialArray = [1,2,3];

		foreach ($subgroupData as $key => $subGroup) {
			# code...
			shuffle($serialArray);
			$gw = 12;
			foreach ($serialArray as $key => $serial) {
				# code...
				$fixtureEntrySql = 'insert into ffpb_matches(gameweek, entry1, entry2) values('. $gw .','. $subGroup[0]['team_id'] .','. $subGroup[$serial]['team_id'] .')';
				$this->ffpbBattleOfRoyal->query($fixtureEntrySql);
				$otherTeams = array_diff($serialArray, [$serial]);
				$remainigTeams = [];
				foreach ($otherTeams as $key => $value) {
					# code...
					$remainigTeams[] = $value;
				}
				debug($remainigTeams);
				$fixtureEntrySql = 'insert into ffpb_matches(gameweek, entry1, entry2) values('. $gw .','. $subGroup[$remainigTeams[0]]['team_id'] .','. $subGroup[$remainigTeams[1]]['team_id'] .')';
				$this->ffpbBattleOfRoyal->query($fixtureEntrySql);

				$gw++;
			}
		}
	}

	public function battleOfRoyalsResults($playerID = 1780403){
		$this->autoLayout = false;
		ini_set('max_execution_time', 0); 
		$i = 1;

		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from ffpb_teams where subgroup_id = ' . $groupID;
		$result = $this->ffpbBattleOfRoyal->query($getTeamsDetailsByGroupSql);
		$isCurrentGwEnded = $result[0]['ffpb_teams']['is_current_gw_ended'];
		$presentCurrentGw = $actualCurrentGw = $result[0]['ffpb_teams']['current_gameweek'];
		// $presentCurrentGw = 13;
		$requestedURL = "https://fantasy.premierleague.com/drf/entry/1780403/event/".$presentCurrentGw."/picks";
		$response = file_get_contents($requestedURL);
		$response = json_decode($response, true);

		// $this->League->updatePointTableByGw(9);
		// debug($response);
		// exit();
		

		if(!$response['event']['is_current']){
			$gw = $presentCurrentGw;
			do{
				$gw++;
				$this->ffpbBattleOfRoyal->updatePointTableByGw($gw);

				$requestedURL = "https://fantasy.premierleague.com/drf/entry/1780403/event/".$gw."/picks";
				$response = file_get_contents($requestedURL);
				$response = json_decode($response, true);
			}while(!$response['event']['is_current']);
			$actualCurrentGw = $response['event']['id'];

			$updateCurrentGwSql = 'update ffpb_teams set current_gameweek = ' . $actualCurrentGw . ', is_current_gw_ended = false';
			$this->ffpbBattleOfRoyal->query($updateCurrentGwSql);
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
				// $this->ffpbBattleOfRoyal->updatePointTableByGw($actualCurrentGw);
				$updateCurrentGwStatusSql = 'update ffpb_teams set is_current_gw_ended = true';
				$this->ffpbBattleOfRoyal->query($updateCurrentGwStatusSql);

			}
		}

		$getSubGroupsSql = 'select * from ffpb_subgroups';
		$subGroups = $this->ffpbBattleOfRoyal->query($getSubGroupsSql);

		$this->set(compact('actualCurrentGw', 'subGroups'));



	}

	

	public function getPointTable($groupID = 1){
		$this->autoLayout = false;
		$this->autoRender = false;
		$groupID = $_POST['groupId'];

		$getTeamsDetailsByGroupSql = 'select * from ffpb_teams where subgroup_id = ' . $groupID;
		$result = $this->ffpbBattleOfRoyal->query($getTeamsDetailsByGroupSql);

		echo json_encode($result);
	}


	public function enterPlayerFixture(){
		$this->autoLayout = false;
		$this->autoRender = false;
		$getPlayersFixtureSql = 'select * from ffpb_players left outer join ffpb_teams on ffpb_players.team_id = ffpb_teams.team_id left outer join ffpb_matches on ffpb_teams.team_id = ffpb_matches.entry1';
		$result = $this->ffpbBattleOfRoyal->query($getPlayersFixtureSql);

		// debug($result);
		// exit();

		foreach ($result as $key => $value) {
			# code...
			if($value['ffpb_matches']['id'] != null){
				$playerFixtureEntrySql = 'insert into ffpb_players_in_match (team_id, player_id, match_id) values ('. $value['ffpb_teams']['team_id'] . ','  . $value['ffpb_players']['player_id'] . ',' . $value['ffpb_matches']['id'] . ')';
				
			// debug($playerFixtureEntrySql);
			// exit();
				$this->ffpbBattleOfRoyal->query($playerFixtureEntrySql);
			}
			}
				

		
	}

	public function getLiveMatchPoints($gw = 8){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 
		$i = 1;
		$actualCurrentGw = $gw;

		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from ffpb_teams where subgroup_id = ' . $groupID;
		$result = $this->ffpbBattleOfRoyal->query($getTeamsDetailsByGroupSql);
		$isCurrentGwEnded = $result[0]['ffpb_teams']['is_current_gw_ended'];

		$requestedURL = "https://fantasy.premierleague.com/drf/event/".$gw."/live";
		$response = file_get_contents($requestedURL);
		$liveGameData = json_decode($response, true);

		$getMatchesByGwSql = 'select * from ffpb_matches left outer join ffpb_teams t1 on t1.team_id = entry1 left outer join ffpb_teams t2 on t2.team_id = entry2  where gameweek = ' . $actualCurrentGw ;
		$currentGwMatches = $this->ffpbBattleOfRoyal->query($getMatchesByGwSql);
		$allMatchesSet = '';
		foreach ($currentGwMatches as $key => $match) {
			$allMatchesSet.= $match['ffpb_matches']['id'] . ',';
		}
		$allMatchesSet = rtrim($allMatchesSet);
		
		$getPlayersSql = 'select * from ffpb_players';
		$players = $this->ffpbBattleOfRoyal->query($getPlayersSql);

		$teamData = array();

		if(!$isCurrentGwEnded){
			foreach ($players as $key => $player) {
				# code...
				$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['ffpb_players']['player_code']."/event/".$actualCurrentGw."/picks";
				$response = file_get_contents($requestedURL);
				$response = json_decode($response, true);

				$playerPoint = 0;
				foreach ($response['picks'] as $key => $value) {
					# code...
					if($value['position'] <= 11){
						$playerPoint+= $liveGameData['elements'][$value['element']]['stats']['total_points'] * $value['multiplier'];
					}
				}

				$playerData =array();
				$playerData['name'] = $player['ffpb_teams']['player_name'];
				$playerData['entry_point'] = $playerPoint;
				$playerData['hit_point'] = $response['entry_history']['event_transfers_cost'];
				$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['ffpb_players']['player_code'].'/event/'.$actualCurrentGw;

				$teamData[$player['ffpb_teams']['team_id']][] = $playerData;

			// 	debug($player);
			// 	debug($response);
			// exit();

			}
		}
		else{
			$getPlayersFixtureSql = "select * from ffpb_players left outer join ffpb_players_in_match on ffpb_players.player_id = ffpb_players_in_match.player_id where find_in_set(match_id,'" . $allMatchesSet . "')>0";
			$players = $this->ffpbBattleOfRoyal->query($getPlayersFixtureSql);
			// debug($players);
			// exit();

			foreach ($players as $key => $player) {
				# code...

				$playerData =array();
				$playerData['name'] = $player['ffpb_teams']['player_name'];
				$playerData['entry_point'] = $player['ffpb_players_in_match']['score'];
				$playerData['hit_point'] = $player['ffpb_players_in_match']['hit'];
				$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['ffpb_teams']['player_code'].'/event/'.$actualCurrentGw;

				$teamData[$player['ffpb_teams']['team_id']][] = $playerData;

			// 	debug($player);
			// 	debug($response);
			// exit();

			}

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
			
			$tempData['team1Score'] = $team1Score;

			$team2Score = 0;
			foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
				# code...
				$team2Score+= ($player['entry_point'] - $player['hit_point']);
			}
			
			$tempData['team2Score'] = $team2Score;
			$tempData['team1Players'] = $teamData[$match['t1']['team_id']];
			$tempData['team2Players'] = $teamData[$match['t2']['team_id']];

			$tempData['blank'] = "        ";

			$liveData['data'][] = $tempData;
		}

		echo json_encode($liveData);
	}

	public function getFixtureByGw($gw = 8){
		$this->autoLayout = false;
		$this->autoRender = false;
		ini_set('max_execution_time', 0); 
		$i = 1;
		$groupID = 1;
		$getTeamsDetailsByGroupSql = 'select * from ffpb_teams where subgroup_id = ' . $groupID;
		$result = $this->ffpbBattleOfRoyal->query($getTeamsDetailsByGroupSql);
		$presentCurrentGw = $actualCurrentGw = $result[0]['ffpb_teams']['current_gameweek'];
		if($presentCurrentGw > $gw){
			$presentCurrentGw = $gw;
		}
		$actualCurrentGw = $gw;

		$getMatchesByGwSql = 'select * from ffpb_matches left outer join ffpb_teams t1 on t1.team_id = entry1 left outer join ffpb_teams t2 on t2.team_id = entry2 left outer join ffpb_subgroups on t1.subgroup_id = ffpb_subgroups.id where gameweek = ' . $actualCurrentGw . ' order by t1.team_id';
		$currentGwMatches = $this->ffpbBattleOfRoyal->query($getMatchesByGwSql);

		$allMatchesSet = '';
		foreach ($currentGwMatches as $key => $match) {
			$allMatchesSet.= $match['ffpb_matches']['id'] . ',';
		}
		$allMatchesSet = rtrim($allMatchesSet, ',');
		
		$getPlayersFixtureSql = "select * from ffpb_players left outer join ffpb_players_in_match on ffpb_players.player_id = ffpb_players_in_match.player_id where find_in_set(match_id,'" . $allMatchesSet . "')>0";
		$players = $this->ffpbBattleOfRoyal->query($getPlayersFixtureSql);
		// debug($allMatchesSet);
		// debug($players);
		// exit();

		$teamData = array();

		foreach ($players as $key => $player) {
			# code...

			$playerData =array();
			$playerData['name'] = $player['ffpb_players']['player_name'];
			$playerData['entry_point'] = $player['ffpb_players_in_match']['score'];
			$playerData['hit_point'] = $player['ffpb_players_in_match']['hit'];
			$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['ffpb_players']['player_code'].'/event/'.$presentCurrentGw;

			$teamData[$player['ffpb_players']['team_id']][] = $playerData;

			// debug($player);
		// 	// debug($response);
		// exit();

		}
		// ksort($teamData);
		// debug($teamData);
		// exit();

		$liveData['data'] = array();
		foreach ($currentGwMatches as $key => $match) {
			# code...c
			$tempData['plusSign'] = '<i class="fa fa-plus-circle" aria-hidden="true"></i>';
			$tempData['team1Name'] = $match['t1']['team_name'];
			$tempData['team1Id'] = $match['t1']['team_id'];
			$tempData['subgroupName'] = $match['ffpb_subgroups']['subgroup_name'];
			$tempData['team2Name'] = $match['t2']['team_name'];
			$tempData['team2Id'] = $match['t2']['team_id'];
			$team1Score = 0;
			foreach ($teamData[$match['t1']['team_id']] as $key => $player) {
				# code...
				$team1Score+= (intval($player['entry_point']) - intval($player['hit_point']));
			}
			$tempData['team1Score'] = $team1Score;

			$team2Score = 0;
			foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
				# code...
				$team2Score+= ($player['entry_point'] - $player['hit_point']);
			}
			
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
		$getPlayersFixtureSql = 'select * from ffpb_players_in_match left outer join ffpb_teams on ffpb_players_in_match.player_id = ffpb_teams.player_id where match_id =' .$matchId . '';
		$players = $this->ffpbBattleOfRoyal->query($getPlayersFixtureSql);
		// debug($players);
		// exit();
		$actualCurrentGw = 9;

		foreach ($players as $key => $player) {
				# code...

				$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['ffpb_teams']['player_code']."/event/".$actualCurrentGw."/picks";
				$response = file_get_contents($requestedURL);
				$response = json_decode($response, true);

				$playerData =array();
				$playerData['name'] = $player['ffpb_teams']['player_name'];
				$playerData['entry_point'] = $response['entry_history']['points'];
				$playerData['hit_point'] = $response['entry_history']['event_transfers_cost'];
				$playerData['link'] = 'https://fantasy.premierleague.com/a/team/'.$player['ffpb_teams']['player_code'].'/event/'.$actualCurrentGw;

				$updatePlayerMatchSql = 'update ffpb_players_in_match set score = ' . intval($playerData['entry_point']) . ', hit = '. intval($playerData['hit_point']) .' where player_id = ' . $player['ffpb_teams']['player_id'] . ' and match_id = ' . $matchId;
				$this->ffpbBattleOfRoyal->query($updatePlayerMatchSql);

			// 	debug($player);
			// 	debug($response);
			// exit();

			}

		
	}

}
