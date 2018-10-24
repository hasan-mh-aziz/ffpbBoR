<?php

App::uses('AppModel', 'Model');
class League extends AppModel {
    public $name = 'players';

    public function updateTeam($playerName, $playerCode){
    	$sql = "update players set player_code = ' . $playerCode . ' where player_name like '" . $playerName ."'";
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
		$getMatchesByGwSql = 'select * from matches left outer join teams t1 on t1.team_id = entry1 left outer join teams t2 on t2.team_id = entry2  where gameweek = ' . $givenGw ;
		$currentGwMatches = $this->query($getMatchesByGwSql);
		
		$getPlayersSql = 'select * from players';
		$players = $this->query($getPlayersSql);

		// $requestedURL = "https://fantasy.premierleague.com/drf/event/".$givenGw."/live";
		// $response = file_get_contents($requestedURL);
		// $liveGameData = json_decode($response, true);

		$teamData = array();

		foreach ($players as $key => $player) {
			# code...
			$requestedURL = "https://fantasy.premierleague.com/drf/entry/".$player['players']['player_code']."/event/".$givenGw."/picks";
			$response = file_get_contents($requestedURL);
			$response = json_decode($response, true);

			$teamData[$player['players']['team_id']][$player['players']['player_name']]['name'] = $player['players']['player_name'];
			$teamData[$player['players']['team_id']][$player['players']['player_name']]['entry_point'] = $response['entry_history']['points'];
			$teamData[$player['players']['team_id']][$player['players']['player_name']]['hit_point'] = $response['entry_history']['event_transfers_cost'];
			$teamData[$player['players']['team_id']][$player['players']['player_name']]['player_id'] = $player['players']['player_id'];
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
				$playerPoint = (intval($player['entry_point']) - intval($player['hit_point']));
				$team1Score+= $playerPoint;

			$updatePlayerMatchSql = 'update players_in_match set score = ' . intval($player['entry_point']) . ', hit = '. intval($player['hit_point']) .' where player_id = ' . $player['player_id'] . ' and match_id = ' . $match['matches']['id'];
			$this->query($updatePlayerMatchSql);
			}
			$team2Score = 0;
			foreach ($teamData[$match['t2']['team_id']] as $key => $player) {
				# code...
				$playerPoint = (intval($player['entry_point']) - intval($player['hit_point']));
				$team2Score+= $playerPoint;

				$updatePlayerMatchSql = 'update players_in_match set score = ' . intval($player['entry_point']) . ', hit = '. intval($player['hit_point']) .' where player_id = ' . $player['player_id'] . ' and match_id = ' . $match['matches']['id'];
				$this->query($updatePlayerMatchSql);
			}
			if($match['t1']['team_id'] === '1'){
				$team1Score = $team1Score*5/4;
			}
			else if($match['t2']['team_id'] === '1'){
				$team2Score = $team2Score*5/4;
			}

			if($team1Score > $team2Score){
				$updateTeamStatSql = 'update teams set played = played + 1,win = win + 1, score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update teams set played = played + 1,score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
			}
			else if($team1Score < $team2Score){
				$updateTeamStatSql = 'update teams set played = played + 1,win = win + 1, score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update teams set played = played + 1,score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
			}
			else{
				$updateTeamStatSql = 'update teams set played = played + 1,draw = draw + 1 where team_id = ' . $match['t1']['team_id'] . ' or team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update teams set score_for = score_for + '.$team1Score.', score_against = score_against + '.$team2Score.' where team_id = ' . $match['t1']['team_id'];
				$this->query($updateTeamStatSql);
				$updateTeamStatSql = 'update teams set score_for = score_for + '.$team2Score.', score_against = score_against + '.$team1Score.' where team_id = ' . $match['t2']['team_id'];
				$this->query($updateTeamStatSql);
			}

			$updateMatchSql = 'update matches set entry1_points = ' . $team1Score . ',entry2_points = ' . $team2Score . ' where id = ' . $match['matches']['id'];
			$this->query($updateMatchSql);
		}
	}
}