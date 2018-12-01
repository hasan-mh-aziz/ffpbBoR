<?php


App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel');
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel'.DS.'IOFactory.php'));


class FfpbMatchesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('FfpbTeam', 'FfpbMatch', 'FfpbPlayer');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */


	public function setUpFixtures() {
		$this->layout = 'ffpbBoR';
		// $this->autoRender = false;

	}

	public function addMatchesByGameweek() {
		$this->autolayout = false;
		$this->autoRender = false;

		$teams = $this->FfpbTeam->find('all');
	    	$matches = array();
	    	$teamSubgroups = array();
	    	$entry2SubgroupPostion = $_POST['entry2SubgroupPostion'];
	    	$entry1SubgroupPostion = $_POST['entry1SubgroupPostion'];

		foreach ($teams as $key => $team) {
			if($team['FfpbTeam']['subgroup_entry_position'] == $entry2SubgroupPostion || $team['FfpbTeam']['subgroup_entry_position'] == $entry1SubgroupPostion) {
				$teamSubGroup = $team['FfpbTeam']['group_id'] . $team['FfpbTeam']['subgroup_id'];
				if (!isset($teamSubgroups[$teamSubGroup])) {
					$teamSubgroups[$teamSubGroup] = array();
				}
				array_push($teamSubgroups[$teamSubGroup], $team['FfpbTeam']['id']);
				// debug($team);
	   //  		exit();
			}
		}

		foreach ($teamSubgroups as $key => $teamSubGroup) {
			if(isset($teamSubGroup[0]) && isset($teamSubGroup[1])) {
				array_push($matches, array('gameweek' => $_POST['gameWeek'], 'entry1' => $teamSubGroup[0], 'entry2' => $teamSubGroup[1]));
			}
		}
		$this->FfpbMatch->saveMany($matches);

		echo json_encode($matches);


	}

	public function showLiveResults() {
		$this->layout = 'ffpbBoR';
		// $this->autoRender = false;

	}

	public function getMatchesByGw($gameweek = 10) {
		$this->autolayout = false;
		$this->autoRender = false;

		$matches = $this->FfpbMatch->find('all',
			array(
				'conditions' => array('FfpbMatch.gameweek' => $gameweek),
				'recursive' => 0,
				));
	    echo json_encode($matches);


	}


	public function getMatchesByGwGroupAndSubgroup($gw, $group_id, $subgroup_id) {
		$this->autoLayout = false;
		$this->autoRender = false;

		$matches = $this->FfpbMatch->find('all',
			array(
				'conditions' => array('FfpbMatch.gameweek' => $gw),
				'recursive' => 1
			)
		);

		$filteredMatches = array_filter($matches, function($match) use ($group_id, $subgroup_id){
			if($match['entry1']['group_id'] == $group_id && $match['entry1']['subgroup_id'] == $subgroup_id){
				return true;
			} else if($match['entry2']['group_id'] == $group_id && $match['entry2']['subgroup_id'] == $subgroup_id){
				return true;
			}
			return false;
		});
		$playerIds = array_reduce($filteredMatches, function($accumalator, $match){
			$playerIdsInMatch = array_map(function($player){
				return $player['player_id'];
			}, $match['playerInMatch']);
			return array_merge($accumalator ,$playerIdsInMatch);
		}, []);
		$players = $this->FfpbPlayer->findAllByPlayerId($playerIds);
		foreach ($filteredMatches as $key => $match) {
			# code...
			$filteredMatches[$key]['entry1']['players'] = array();
			$filteredMatches[$key]['entry2']['players'] = array();
		}
		foreach ($players as $key => $player) {
			# code...
			foreach ($filteredMatches as $key => $match) {
				# code...
				if($player['team']['id'] == $match['entry1']['id']){
					array_push($filteredMatches[$key]['entry1']['players'], $player['FfpbPlayer']);
				} else if($player['team']['id'] == $match['entry2']['id']){
					array_push($filteredMatches[$key]['entry2']['players'], $player['FfpbPlayer']);
				}
			}
		}
		// debug($filteredMatches);
		echo json_encode($filteredMatches);


	}

	public function update() {
		$this->layout = 'ffpbBoR';
		// debug($this->authorizedPasscodes);
	}

	public function updateMatchesByMatchesData() {
		$this->autolayout = false;
		$this->autoRender = false;
		// print_r($this->request->data);

		if(in_array($this->request->data['passcode'], $this->authorizedPasscodes)) {
			$matchesData = $this->request->data['matchesData'];
			foreach ($matchesData as $key => $matchData) {
				$matchDataToUpdate = array(
					'entry1_points' => $matchData['entry1Points'],
					'entry2_points' => $matchData['entry2Points']
					);
				$this->FfpbMatch->read(null, $matchData['matchId']);
				$this->FfpbMatch->set($matchDataToUpdate);
				$this->FfpbMatch->save();
			}

			echo json_encode($matchesData);

		} else {
			echo json_encode('unauthorized request;');
		}


	}

	public function showFixtures($gameweek) {
		$this->layout = 'ffpbBoR';

	}

	public function showFixturesBog($gameweek) {
		$this->layout = 'ffpbBoR';

	}


	public function updateMatchesByGameweek($gameweek = 0) {
		$this->layout = 'ffpbBoR';

		$this->setJsVariables('currentGw', $gameweek);
		$this->setJsVariables('excludedPlayerFplCodes', array());
		$this->set(compact('gameweek'));

	}

	public function addFixtureBorAfterGroup($gameweek = 0){
		$this->layout = 'ffpbBoR';

		$teams = $this->FfpbTeam->find('all');
		$teamNames = array();
    	$teamSubgroups = array();
    	$teamsByGroupBoR = array(
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
			if (!isset($teamsByGroupBoR[$currentTeamGroupId][$currentTeamSubGroupId])) {
				$teamsByGroupBoR[$currentTeamGroupId][$currentTeamSubGroupId] = array();
			}
			$tourneyPosition = $value['FfpbTeam']['inBoR'];
			if($tourneyPosition > 0){
				$teamsByGroupBoR[$currentTeamGroupId][$currentTeamSubGroupId][$tourneyPosition] = $value;
			}
		}

		// debug($teamsByGroupBoR);
		$matchEntry1s = array('A1', 'C1', 'E1', 'G1', 'H1', 'J1', 'L1', 'B1', 'D1', 'F1', 'I1', 'K1', 'M1');
		$matchEntry2s = array('B2', 'D2', 'F2', 'I2', 'G2', 'K2', 'M2', 'A2', 'C2', 'E2', 'H2', 'J2', 'L2');
		$matcheToAdd = array();

		foreach ($teamsByGroupBoR as $group_id => $teamsInGroupBoR) {
			foreach ($matchEntry1s as  $key => $entry1) {
				# code...
				$entry2 = $matchEntry2s[$key];
				array_push($matcheToAdd, array('gameweek' => $gameweek, 'entry1' => $teamsInGroupBoR[$entry1[0]][$entry1[1]]['FfpbTeam']['id'], 'entry2' => $teamsInGroupBoR[$entry2[0]][$entry2[1]]['FfpbTeam']['id']));
				array_push($matcheToAdd, array('gameweek' => $gameweek + 1, 'entry1' => $teamsInGroupBoR[$entry1[0]][$entry1[1]]['FfpbTeam']['id'], 'entry2' => $teamsInGroupBoR[$entry2[0]][$entry2[1]]['FfpbTeam']['id']));
			}
		}
			
		debug($matcheToAdd);

		$this->FfpbMatch->saveMany($matcheToAdd);
	}



	public function addFixtureBoGAfterGroup($gameweek = 0){
		$this->layout = 'ffpbBoR';

		$teams = $this->FfpbTeam->find('all');
		$teamNames = array();
    	$teamSubgroups = array();
    	$teamsByGroupBoG = array(
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
			if (!isset($teamsByGroupBoG[$currentTeamGroupId][$currentTeamSubGroupId])) {
				$teamsByGroupBoG[$currentTeamGroupId][$currentTeamSubGroupId] = array();
			}
			$tourneyPosition = $value['FfpbTeam']['inBoG'];
			if($tourneyPosition > 0){
				$teamsByGroupBoG[$currentTeamGroupId][$currentTeamSubGroupId][$tourneyPosition] = $value;
			}
		}

		// debug($teamsByGroupBoG);
		$matchEntry1s = array('A1', 'C1', 'E1', 'G1', 'H1', 'J1', 'L1', 'B1', 'D1', 'F1', 'I1', 'K1', 'M1');
		$matchEntry2s = $matchEntry1s;
		$matcheToAdd = array();

		foreach ($matchEntry1s as  $key => $entry1) {
			# code...
			$entry2 = $matchEntry2s[$key];
			// array_push($matcheToAdd, array('gameweek' => $gameweek, 'entry1' => $teamsByGroupBoG['1'][$entry1[0]][$entry1[1]]['FfpbTeam']['id'], 'entry2' => $teamsByGroupBoG['2'][$entry2[0]][$entry2[1]]['FfpbTeam']['id']));
			array_push($matcheToAdd, array('gameweek' => $gameweek + 1, 'entry1' => $teamsByGroupBoG['1'][$entry1[0]][$entry1[1]]['FfpbTeam']['id'], 'entry2' => $teamsByGroupBoG['2'][$entry2[0]][$entry2[1]]['FfpbTeam']['id']));
		}
			
		debug($matcheToAdd);
		
		$this->FfpbMatch->saveMany($matcheToAdd);
	}

}
