<?php


App::uses('AppController', 'Controller');


class FfpbPlayersController extends AppController {
	public $uses = array('FfpbPlayer', 'FfpbPlayerInMatch', 'FfpbTeam', 'FfpbMatch');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */


	public function updateAllPlayerName() {
		$this->layout = 'ffpbBoR';

		$players = $this->FfpbPlayer->find('all');

		$this->setJsVariables('players', $players);
	}

	public function updatePlayerByAjax() {
		$this->autolayout = false;
		$this->autoRender = false;

		$playersDataToUpdate = array();
		foreach ($_POST['playerNames'] as $key => $playerName) {
			$this->FfpbPlayer->read(null, $playerName['playerId']);
			$this->FfpbPlayer->set(array('player_name' => $playerName['playerName']));
			$this->FfpbPlayer->save();
		}

		echo json_encode($playersDataToUpdate);
	}

	public function getPlayersInMatchesByMatchIds(){
		$this->autolayout = false;
		$this->autoRender = false;

		$playersInMatches = $this->FfpbPlayerInMatch->find('all',
			array(
				'conditions' => array('FfpbPlayerInMatch.match_id' => $this->request->query['matchIds']),
				'recursive' => 0,
		));
		echo json_encode($playersInMatches);
	}

	public function insertPlayerMatches() {
		$this->layout = 'ffpbBoR';

		// $players = $this->FfpbPlayer->find('all', array('recursive' => 1));
		$teams = $this->FfpbTeam->find('all');
		$playersMatchesToAdd = array();

		foreach ($teams as $key => $team) {
			foreach ($team['FfpbPlayer'] as $key => $player) {
				foreach ($team['matches1'] as $key => $match) {
					$playerInMatch = array(
						'player_id' => $player['player_id'],
						'match_id' => $match['id'],
					);
					array_push($playersMatchesToAdd, $playerInMatch);
				}
				foreach ($team['matches2'] as $key => $match) {
					$playerInMatch = array(
						'player_id' => $player['player_id'],
						'match_id' => $match['id'],
					);
					array_push($playersMatchesToAdd, $playerInMatch);
				}
			}
		}
		// debug($playersMatchesToAdd);
		$this->FfpbPlayerInMatch->saveMany($playersMatchesToAdd);
	}

	public function updatePlayerInMatchesByMatchesData() {
		// $this->autolayout = false;
		$this->autoRender = false;
		// print_r($this->request->data);

		if(in_array($this->request->data['passcode'], $this->authorizedPasscodes) || true) {
			$playersInMatches = $this->FfpbPlayerInMatch->find('all');
			$playeresInMatchesDataToUpdate = array();
			
			foreach ($playersInMatches as $key => $playerInMatch) {
				$matchId = $playerInMatch['FfpbPlayerInMatch']['match_id'];
				$playerId = $playerInMatch['FfpbPlayerInMatch']['player_id'];
				if(!isset($playeresInMatchesDataToUpdate[$matchId])) {
					$playeresInMatchesDataToUpdate[$matchId] = array();
				}
				$playeresInMatchesDataToUpdate[$matchId][$playerId] = $playerInMatch;
			}
			// debug($playeresInMatchesDataToUpdate);
			$matchesData = $this->request->data['matchesData'];
			foreach ($matchesData as $key => $matchData) {
				$matchId = $matchData['matchId'];
				foreach ($matchData['entry1Players'] as $key => $player) {
					$playerId = $player['player']['player_id'];
					$playerInMatchDataToUpdate = array(
						'earned_point' => $player['playerPoint'],
						'taken_hit' => $player['hitPoint'],
						'used_chip' => $player['activeChip']
						);
					$this->FfpbPlayerInMatch->read(null, $playeresInMatchesDataToUpdate[$matchId][$playerId]['FfpbPlayerInMatch']['id']);
					$this->FfpbPlayerInMatch->set($playerInMatchDataToUpdate);
					$this->FfpbPlayerInMatch->save();
					
				}
				foreach ($matchData['entry2Players'] as $key => $player) {
					$playerId = $player['player']['player_id'];
					$playerInMatchDataToUpdate = array(
						'earned_point' => $player['playerPoint'],
						'taken_hit' => $player['hitPoint'],
						'used_chip' => $player['activeChip']
						);
					$this->FfpbPlayerInMatch->read(null, $playeresInMatchesDataToUpdate[$matchId][$playerId]['FfpbPlayerInMatch']['id']);
					$this->FfpbPlayerInMatch->set($playerInMatchDataToUpdate);
					$this->FfpbPlayerInMatch->save();
					
				}
			}

			echo json_encode($playeresInMatchesDataToUpdate);

		} else {
			echo json_encode('unauthorized request;');
		}


	}

	public function negatePlayersPoints(){
		// $this->layout = 'ffpbBoR';
		$playerFplCodes = array(5705842, 3360349, 1905001, 5741035, 4986175, 5775441, 1334126, 5711416, 5692478);
		$gameweek = 10;

		$players = $this->FfpbPlayer->find('all',
			array(
				'conditions' => array('FfpbPlayer.player_code' => $playerFplCodes),
				'recursive' => 0,
		));
		$players = array_reduce($players, function(&$result, $player){ //key of the array will player_id
		        $result[$player['FfpbPlayer']['player_id']] = $player;
		        return $result;
		    }, array());

		$player_ids = array_map(function ($player){
			return $player['FfpbPlayer']['player_id'];
		} , $players);
		$playersInMatches = $this->FfpbPlayerInMatch->find('all',
			array(
				'conditions' => array('FfpbPlayerInMatch.player_id' => $player_ids),
				'recursive' => 0,
		));
		$playersInMatchesFilteredByGw = array_filter($playersInMatches, function ($playerInMatch) use ($gameweek){
			return $playerInMatch['match']['gameweek'] == $gameweek;
		});
		// debug($players);
		// debug($playersInMatchesFilteredByGw);

		foreach ($playersInMatchesFilteredByGw as $key => $playerInMatch) {
			$playerId = $playerInMatch['FfpbPlayerInMatch']['player_id'];
			$playerPoint = $playerInMatch['FfpbPlayerInMatch']['earned_point'];
			if($playerInMatch['match']['entry1'] == $playerInMatch['player']['team_id']){
				$matchDataToUpdate = array('entry1_points' => ($playerInMatch['match']['entry1_points'] - $playerInMatch['FfpbPlayerInMatch']['earned_point']));
				$opponentTeamId = $playerInMatch['match']['entry2'];
			} else{
				$matchDataToUpdate = array('entry2_points' => ($playerInMatch['match']['entry2_points'] - $playerInMatch['FfpbPlayerInMatch']['earned_point']));
				$opponentTeamId = $playerInMatch['match']['entry1'];
			}
			// $this->FfpbMatch->read(null, $playerInMatch['FfpbPlayerInMatch']['match_id']);
			// $this->FfpbMatch->set($matchDataToUpdate);
			// $this->FfpbMatch->save();

			$teamDataToUpdate = array('score_for' => ($players[$playerId]['team']['score_for'] - $playerPoint));
			debug($teamDataToUpdate);
			$this->FfpbTeam->read(null, $playerInMatch['player']['team_id']);
			$this->FfpbTeam->set($teamDataToUpdate);
			$this->FfpbTeam->save();
			$opponentTeamDataToUpdate = array('score_against' => ($players[$playerId]['team']['score_against'] - $playerPoint));
			$this->FfpbTeam->read(null, $opponentTeamId);
			$this->FfpbTeam->set($opponentTeamDataToUpdate);
			$this->FfpbTeam->save();


			$playerInMatchDataToUpdate = array('earned_point' => 0, 'taken_hit' => 0);		
			// $this->FfpbPlayerInMatch->read(null, $playerInMatch['FfpbPlayerInMatch']['id']);
			// $this->FfpbPlayerInMatch->set($playerInMatchDataToUpdate);
			// $this->FfpbPlayerInMatch->save();
		}
	}


}
