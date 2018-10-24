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
	public $uses = array('FfpbTeam', 'FfpbMatch');

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

	public function getMatchesByGw($gameweek = 9) {
		$this->autolayout = false;
		$this->autoRender = false;

		$matches = $this->FfpbMatch->find('all',
			array(
				'conditions' => array('FfpbMatch.gameweek' => $gameweek),
				'recursive' => 0,
				));
	    echo json_encode($matches);


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


}
