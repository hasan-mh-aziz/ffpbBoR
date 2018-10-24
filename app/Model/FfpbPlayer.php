<?php

App::uses('AppModel', 'Model');
class FfpbPlayer extends AppModel {
    public $name = 'FfpbPlayer';

    public $belongsTo = array(
        'team' =>
            array(
                'className' => 'FfpbTeam',
                // 'joinTable' => 'ingredients_recipes',
                'foreignKey' => 'team_id',
                'unique' => false,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            ),
    );

    public function getAllTeams(){
    	$sql = "select * from ffpb_teams";
    	$teams = $this->query($sql);

    	return $teams;
    }

    public function updateTeamDataFromFile($fileName) {
    	$file = fopen($fileName,"r");
    	fgetcsv($file);

    	$getTeamNameFromTeams = function($team) {
		    return $team['ffpb_teams']['team_name'];
		};

    	$teams = $this->getAllTeams();
    	$teamNames = array_map($getTeamNameFromTeams, $teams);

		while(! feof($file)) {
	  		$teamData = fgetcsv($file);
	  		if (in_array($teamData[1], $teamNames)){
	  			continue;
	  		}
	  		$teamInsertionSql = "insert into ffpb_teams (team_name, current_gameweek) VALUES (". $this->getDataSource()->value($teamData[1]) .", 9)";
	    	debug($teamInsertionSql);
	    	$this->query($teamInsertionSql);

	    	$instertedTeamId = $this->get_sql_insert_id();
	    	$teamleaderInsertionSql = "insert into ffpb_players (is_team_leader, player_code, team_id, fb_id) VALUES (1, ". $teamData[2] . "," . $instertedTeamId . ",'" . $teamData[3] . "')";
	    	$this->query($teamleaderInsertionSql);

	  		$numberOfGeneralMember = 4;
	  		for($i = 1; $i <= $numberOfGeneralMember; $i++) {
	  			$playerInsertionSql = "insert into ffpb_players (player_code, team_id, fb_id) VALUES (". $teamData[($i + 1) * 2] . "," . $instertedTeamId . ",'" . $teamData[($i + 1) * 2 + 1] . "')";
	    		$this->query($playerInsertionSql);
	  		}
	  		// exit();
	  	}

		fclose($file);

    }

    public function updateTeam($playerName, $playerCode){
    	$sql = "update ffpb_players set player_code = ' . $playerCode . ' where player_name like '" . $playerName ."'";
    	debug($sql);
    	$this->query($sql);
    }

    public function getPlayerLiveData($playerCode, $gw){
    	$playerLink = "https://fantasy.premierleague.com/drf/entry/".$playerCode."/event/".$gw."/picks";

    	$response = file_get_contents($playerLink);
		$response = json_decode($response, true);

		return $response;
    }

    public function updatePointTableByGw($givenGw = null){
		$getMatchesByGwSql = 'select * from ffpb_matches left outer join ffpb_teams t1 on t1.team_id = entry1 left outer join ffpb_teams t2 on t2.team_id = entry2  where gameweek = ' . $givenGw ;
		$currentGwMatches = $this->query($getMatchesByGwSql);
		
		$getPlayersSql = 'select * from ffpb_players';
		$players = $this->query($getPlayersSql);

		// $requestedURL = "https://fantasy.premierleague.com/drf/event/".$givenGw."/live";
		// $response = file_get_contents($requestedURL);
		// $liveGameData = json_decode($response, true);

		$teamData = array();

		foreach ($players as $key => $player) {
			# code...
			$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['ffpb_players']['player_code']."/event/".$givenGw."/picks";
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			$teamData[$player['ffpb_players']['team_id']][$player['ffpb_players']['player_name']]['name'] = $player['ffpb_players']['player_name'];
			$teamData[$player['ffpb_players']['team_id']][$player['ffpb_players']['player_name']]['entry_point'] = $response['entry_history']['points'];
			$teamData[$player['ffpb_players']['team_id']][$player['ffpb_players']['player_name']]['hit_point'] = $response['entry_history']['event_transfers_cost'];
			$teamData[$player['ffpb_players']['team_id']][$player['ffpb_players']['player_name']]['player_id'] = $player['ffpb_players']['player_id'];
		// 	debug($player);
		// 	debug($response);
		// exit();

		}

		// debug($teamData);
		// exit();

		foreach ($currentGwMatches as $key => $match) {
			# code...c
			$team1Score = 0;
			foreach ($teamData[$match['t1']['team_id']] as $key => $player) {
				# code...
				if($givenGw === 12){
					$player['hit_point'] = 0;
				}
				$playerPoint = (intval($player['entry_point']) - intval($player['hit_point']));
				$team1Score+= $playerPoint;

			$updatePlayerMatchSql = 'update ffpb_players_in_match set score = ' . intval($player['entry_point']) . ', hit = '. intval($player['hit_point']) .' where player_id = ' . $player['player_id'] . ' and match_id = ' . $match['ffpb_matches']['id'];
			$this->query($updatePlayerMatchSql);
			}
			$team2Score = 0;
			foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
				# code...
				
				if($givenGw === 12){
					$player['hit_point'] = 0;
				}
				$playerPoint = (intval($player['entry_point']) - intval($player['hit_point']));
				$team2Score+= $playerPoint;

				$updatePlayerMatchSql = 'update ffpb_players_in_match set score = ' . intval($player['entry_point']) . ', hit = '. intval($player['hit_point']) .' where player_id = ' . $player['player_id'] . ' and match_id = ' . $match['ffpb_matches']['id'];
				$this->query($updatePlayerMatchSql);
			}
			// if($match['t1']['team_id'] === '1'){
			// 	$team1Score = $team1Score*5/4;
			// }
			// else if($match['t2']['team_id'] === '1'){
			// 	$team2Score = $team2Score*5/4;
			// }

			if($team1Score > $team2Score){
				$updateTeamStatSql = 'update ffpb_teams set played = played + 1,win = win + 1, score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update ffpb_teams set played = played + 1,score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
			}
			else if($team1Score < $team2Score){
				$updateTeamStatSql = 'update ffpb_teams set played = played + 1,win = win + 1, score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update ffpb_teams set played = played + 1,score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
			}
			else{
				$updateTeamStatSql = 'update ffpb_teams set played = played + 1,draw = draw + 1 where team_id = ' . $match['t1']['team_id'] . ' or team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update ffpb_teams set score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update ffpb_teams set score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
			}

			$updateMatchSql = 'update ffpb_matches set entry1_points = ' . $team1Score . ',entry2_points = ' . $team2Score . ' where id = ' . $match['ffpb_matches']['id'];
			$this->query($updateMatchSql);
		}
	}
}