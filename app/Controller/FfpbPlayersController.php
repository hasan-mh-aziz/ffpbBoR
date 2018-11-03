<?php


App::uses('AppController', 'Controller');


class FfpbPlayersController extends AppController {
	public $uses = array('FfpbPlayer', 'FfpbPlayerInMatch', 'FfpbTeam');

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



}
