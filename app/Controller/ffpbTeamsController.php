<?php


App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');

class FfpbTeamsController extends AppController {

	public $uses = array('FfpbTeam', 'FfpbMatch');

	
	public function collectTeamData() {
		$this->autoLayout = false;
		$this->autoRender = false;

		$this->FfpbTeam->updateTeamDataFromFile('files/teamData.csv');

	}

	public function getAllTeam() {
		$this->autoLayout = false;
		$this->autoRender = false;

		echo json_encode($this->FfpbTeam->find('all'));

	}


	public function getTeamsByGropuAndSubGroup($group_id, $subgroup_id) {
		$this->autoLayout = false;
		$this->autoRender = false;

		$teams = $this->FfpbTeam->find('all', array('conditions' => array('FfpbTeam.group_id' => $group_id, 'FfpbTeam.subgroup_id' => $subgroup_id)));

		echo json_encode($teams);

	}

	public function setGroupOfTeam() {
		$this->layout = 'ffpbBoR';
		//$this->autoRender = false;

		$teams = $this->FfpbTeam->find('all');
    	$teamNames = array();
    	$teamSubgroups = array();
    	$teamsByGroup = array(
    		'1' => array(),
    		'2' => array(),
    		);
    	$possibleGroups = array('1', '2');
		foreach ($teams as $key => $value) {
			$currentTeamGroupId = $value['FfpbTeam']['group_id'];
			$currentTeamSubGroupId = $value['FfpbTeam']['subgroup_id'];
			$currentTeamSubGroupEntryPosition = $value['FfpbTeam']['subgroup_entry_position'];
			$teamNames[$value['FfpbTeam']['id']] = $value['FfpbTeam']['team_name'];
			$teamSubgroups[$value['FfpbTeam']['id']] = $currentTeamGroupId  . $currentTeamSubGroupId;
			if (true) {
				// debug($currentTeamGroupId);
				// debug($teamsByGroup[$currentTeamGroupId]);
				if (!isset($teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId])) {
					$teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId] = array();
				}
				array_push($teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId], $value);
				// $teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId][$currentTeamSubGroupEntryPosition] = $value;
			} 
		}
		
		foreach ($teamsByGroup as $group => $subGroups ) {
			ksort($teamsByGroup[$group]);
			foreach ($subGroups as $subGroupNo => $subGroup) {
				ksort($teamsByGroup[$group][$subGroupNo]);
			}
		}
		// debug($teamsByGroup );
		

		if ($this->request->is(array('post', 'put'))) {
		    //$isDuplicateSubGroup = array_search($this->request->data['FfpbTeam']['group_id'] . $this->request->data['FfpbTeam']['subgroup_id'], $teamSubgroups);
		    $isDuplicateSubGroup  = false;

		    //debug($this->referer());
		    $currentData = $this->request->data;
		    if (!$isDuplicateSubGroup) {
		    	$this->FfpbTeam->save($this->request->data);
			    $this->Session->setFlash('The team has been updated.');
		    } else {
		    	$this->Session->setFlash('This sub group has been already selected for team-' . $teamNames[$isDuplicateSubGroup]);
		    }
		    return $this->redirect(array(
			'controller' => $this->params['controller'],
			'action' => $this->params['action'],
			'?' => array(
				// 'group_id' => $this->request->data['FfpbTeam']['group_id'],
				// 'subgroup_id' => $this->request->data['FfpbTeam']['subgroup_id'],
				'id' => $this->request->data['FfpbTeam']['id'],
				'subgroup_entry_position' => $this->request->data['FfpbTeam']['subgroup_entry_position'],
				),
			));
			    
		}

    	$this->set(compact('teamNames', 'teamsByGroup' ));

	}
	
	public function viewTeams() {
		$this->layout = 'ffpbBoR';
		//$this->autoRender = false;
		$currentGwData = json_decode(file_get_contents('https://fantasy.premierleague.com/drf/entry/300023'));
		$currentGameweek = $currentGwData->entry->current_event;
		$teams = $this->FfpbTeam->find('all');
		if($teams[0]['FfpbTeam']['current_gameweek'] != $currentGameweek) {
			$this->FfpbTeam->updateAll(array('current_gameweek' => $currentGameweek));
			return $this->redirect(array(
				'controller' => $this->params['controller'],
				'action' => $this->params['action']
			));
		}
    	$teamNames = array();
    	$teamSubgroups = array();
    	$teamsByGroup = array(
    		'1' => array(),
    		'2' => array(),
    		);
    	$possibleGroups = array('1', '2');
		foreach ($teams as $key => $value) {
			$currentTeamGroupId = $value['FfpbTeam']['group_id'];
			$currentTeamSubGroupId = $value['FfpbTeam']['subgroup_id'];
			$currentTeamSubGroupEntryPosition = $value['FfpbTeam']['subgroup_entry_position'];
			$teamNames[$value['FfpbTeam']['id']] = $value['FfpbTeam']['team_name'];
			$teamSubgroups[$value['FfpbTeam']['id']] = $currentTeamGroupId  . $currentTeamSubGroupId;
			if (true) {
				if (!isset($teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId])) {
					$teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId] = array();
				}
				array_push($teamsByGroup[$currentTeamGroupId][$currentTeamSubGroupId], $value);
			} 
		}
		
		foreach ($teamsByGroup as $group => $subGroups ) {
			ksort($teamsByGroup[$group]);
			foreach ($subGroups as $subGroupNo => $subGroup) {
				ksort($teamsByGroup[$group][$subGroupNo]);
			}
		}
		// debug($teamsByGroup);


    	$this->set(compact('teamNames', 'teamsByGroup' ));

	}


	
	public function compareTeams() {
		$this->layout = 'ffpbBoR';
		//$this->autoRender = false;

		$teams = $this->FfpbTeam->find('all');
	    $teamsPlayers = array();
		foreach ($teams as $key => $value) {
			$teamNames[$value['FfpbTeam']['id']] = $value['FfpbTeam']['team_name'];
			$teamsPlayers[$value['FfpbTeam']['id']] = $value['FfpbPlayer'];
		}

		// debug($teamsPlayers);
		array_walk_recursive($teamsPlayers, function(&$leaf) {
		    if (is_string($leaf)){
		       $leaf = Sanitize::clean($leaf, array('encode' => true));
		       $leaf = trim($leaf);
		    }

		});
		$this->setJsVariables('teamsPlayers', ($teamsPlayers));
    	$this->set(compact('teamNames', 'teamsByGroup' ));

	}

	
	public function pointTables() {
		$this->layout = 'ffpbBoR';
		//$this->autoRender = false;

		$teams = $this->FfpbTeam->find('all');
		$teamNames = array();
    	$teamSubgroups = array();
    	$teamsByGroup = array(
    		'A' => array(),
    		'B' => array(),
    		);
    	$possibleGroups = array('A', 'B');
		foreach ($teams as $key => $value) {
			$currentTeamGroupId = $value['FfpbTeam']['group_id'];
			$currentTeamSubGroupId = $value['FfpbTeam']['subgroup_id'];
			$currentTeamSubGroupEntryPosition = $value['FfpbTeam']['subgroup_entry_position'];
			$teamNames[$value['FfpbTeam']['id']] = $value['FfpbTeam']['team_name'];
			$teamSubgroups[$value['FfpbTeam']['id']] = $currentTeamGroupId  . $currentTeamSubGroupId;
			if ($currentTeamSubGroupId != 0) {
				if (!isset($teamsByGroup[$possibleGroups[$currentTeamGroupId - 1]][$currentTeamSubGroupId])) {
					$teamsByGroup[$possibleGroups[$currentTeamGroupId - 1]][$currentTeamSubGroupId] = array();
				}
				//array_push($teamsByGroup[$possibleGroups[$currentTeamGroupId  - 1]][$currentTeamSubGroupId], $value['FfpbTeam']['team_name']);
				$teamsByGroup[$possibleGroups[$currentTeamGroupId  - 1]][$currentTeamSubGroupId][$currentTeamSubGroupEntryPosition] = $value;
			} 
		}

		function my_sort($a,$b) {
			// debug($a);
			$team1Point = $a['FfpbTeam']['win']*3 + $a['FfpbTeam']['draw'];
			$team2Point = $b['FfpbTeam']['win']*3 + $b['FfpbTeam']['draw'];
			if ($team1Point == $team2Point) return 0;
			return ($team1Point < $team1Point) ? 1:-1;
		}
		
		foreach ($teamsByGroup as $group => $subGroups ) {
			ksort($teamsByGroup[$group]);
			foreach ($subGroups as $subGroupNo => $subGroup) {
				// ksort($teamsByGroup[$group][$subGroupNo]);
				uasort($teamsByGroup[$group][$subGroupNo],"my_sort");
			}
		}
		// debug($teamsByGroup);

    	$this->set(compact('teamNames', 'teamsByGroup' ));

	}


	public function updateTeams() {
		$this->autolayout = false;
		$this->autoRender = false;
		// print_r($this->request->data);

		if(in_array($this->request->data['passcode'], $this->authorizedPasscodes)) {
			$teams = $this->FfpbTeam->find('all');
			$teamsPresentdata = array();

			foreach ($teams as $key => $value) {
				$teamsPresentdata[$value['FfpbTeam']['id']] = $value['FfpbTeam'];
			}
			$matchesData = $this->request->data['matchesData'];
			$teamsDataToUpdate = array();
			foreach ($matchesData as $key => $matchData) {
				$entry1Id = $matchData['entry1Id'];
				$entry2Id = $matchData['entry2Id'];
				// debug($entry1Id);
				// debug($matchData['entry1Points']);
				$teamsDataToUpdate[$entry1Id] = array(
					'score_against' => $teamsPresentdata[$entry1Id]['score_against'] + $matchData['entry2Points'],
					'score_for' => $teamsPresentdata[$entry1Id]['score_for'] + $matchData['entry1Points'],
				);
				$teamsDataToUpdate[$entry2Id] = array(
					'score_against' => $teamsPresentdata[$entry2Id]['score_against'] + $matchData['entry1Points'],
					'score_for' => $teamsPresentdata[$entry2Id]['score_for'] + $matchData['entry2Points'],
				);

				$teamsDataToUpdate[$entry1Id]['played'] = $teamsPresentdata[$entry1Id]['played'] + 1;
				$teamsDataToUpdate[$entry2Id]['played'] = $teamsPresentdata[$entry2Id]['played'] + 1;

				if($matchData['entry1Points'] > $matchData['entry2Points']){
					$teamsDataToUpdate[$entry1Id]['win'] = $teamsPresentdata[$entry1Id]['win'] + 1;
				} else if($matchData['entry1Points'] < $matchData['entry2Points']){
					$teamsDataToUpdate[$entry2Id]['win'] = $teamsPresentdata[$entry1Id]['win'] + 1;
				} else {
					$teamsDataToUpdate[$entry1Id]['draw'] = $teamsPresentdata[$entry1Id]['draw'] + 1;
					$teamsDataToUpdate[$entry2Id]['draw'] = $teamsPresentdata[$entry2Id]['draw'] + 1;
				}

				$teamsDataToUpdate[$entry1Id]['current_gameweek_score'] = $matchData['entry1Points'];
				$teamsDataToUpdate[$entry2Id]['current_gameweek_score'] = $matchData['entry2Points'];
				$teamsDataToUpdate[$entry1Id]['is_current_gw_ended'] = 1;
				$teamsDataToUpdate[$entry2Id]['is_current_gw_ended'] = 1;
			}

			foreach ($teamsDataToUpdate as $teamId => $teamData) {	
				$this->FfpbTeam->read(null, $teamId);
				$this->FfpbTeam->set($teamData);
				$this->FfpbTeam->save();
			}
			echo json_encode($teamsDataToUpdate);

		} else {
			echo json_encode('unauthorized request;');
		}

		// $matches = $this->FfpbMatch->find('all',
		// 	array(
		// 		'conditions' => array('FfpbMatch.gameweek' => $gameweek),
		// 		'recursive' => 0,
		// 		));
	 //    echo json_encode($matches);


	}


	public function updateTeamsFromDbByGw($gameweek) {
		$this->autolayout = false;
		$this->autoRender = false;
		// print_r($this->request->data);

		$teams = $this->FfpbTeam->find('all');
		$teamsPresentdata = array();

		foreach ($teams as $key => $value) {
			$teamsPresentdata[$value['FfpbTeam']['id']] = $value['FfpbTeam'];
		}
		$matchesData = $this->FfpbMatch->find('all',
			array(
				'conditions' => array('FfpbMatch.gameweek' => $gameweek),
				'recursive' => 1,
				));
		$teamsDataToUpdate = array();
		// debug($matchesData);
		foreach ($matchesData as $key => $matchData) {
			$entry1Id = $matchData['FfpbMatch']['entry1'];
			$entry2Id = $matchData['FfpbMatch']['entry2'];
			// debug($entry1Id);
			// debug($matchData['entry1Points']);
			$teamsDataToUpdate[$entry1Id] = array(
				'score_against' => $teamsPresentdata[$entry1Id]['score_against'] + $matchData['FfpbMatch']['entry2_points'],
				'score_for' => $teamsPresentdata[$entry1Id]['score_for'] + $matchData['FfpbMatch']['entry1_points'],
			);
			$teamsDataToUpdate[$entry2Id] = array(
				'score_against' => $teamsPresentdata[$entry2Id]['score_against'] + $matchData['FfpbMatch']['entry1_points'],
				'score_for' => $teamsPresentdata[$entry2Id]['score_for'] + $matchData['FfpbMatch']['entry2_points'],
			);

			$teamsDataToUpdate[$entry1Id]['played'] = $teamsPresentdata[$entry1Id]['played'] + 1;
			$teamsDataToUpdate[$entry2Id]['played'] = $teamsPresentdata[$entry2Id]['played'] + 1;

			if($matchData['FfpbMatch']['entry1_points'] > $matchData['FfpbMatch']['entry2_points']){
				$teamsDataToUpdate[$entry1Id]['win'] = $teamsPresentdata[$entry1Id]['win'] + 1;
			} else if($matchData['FfpbMatch']['entry1_points'] < $matchData['FfpbMatch']['entry2_points']){
				$teamsDataToUpdate[$entry2Id]['win'] = $teamsPresentdata[$entry1Id]['win'] + 1;
			} else {
				$teamsDataToUpdate[$entry1Id]['draw'] = $teamsPresentdata[$entry1Id]['draw'] + 1;
				$teamsDataToUpdate[$entry2Id]['draw'] = $teamsPresentdata[$entry2Id]['draw'] + 1;
			}

			$teamsDataToUpdate[$entry1Id]['current_gameweek_score'] = $matchData['FfpbMatch']['entry1_points'];
			$teamsDataToUpdate[$entry2Id]['current_gameweek_score'] = $matchData['FfpbMatch']['entry2_points'];
			$teamsDataToUpdate[$entry1Id]['is_current_gw_ended'] = 1;
			$teamsDataToUpdate[$entry2Id]['is_current_gw_ended'] = 1;
		}

		foreach ($teamsDataToUpdate as $teamId => $teamData) {	
			$this->FfpbTeam->read(null, $teamId);
			$this->FfpbTeam->set($teamData);
			$this->FfpbTeam->save();
		}

		debug($teamsDataToUpdate);


	}


}
