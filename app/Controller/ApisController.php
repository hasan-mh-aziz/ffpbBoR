<?php


App::uses('AppController', 'Controller');


class ApisController extends AppController {

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
	public function enableCORS(){
		$this->autoLayout = false;
		$this->autoRender = false;

		echo file_get_contents($this->params->query['corsUrl']);
	}


	public function checkPasscode(){
		$this->autoLayout = false;
		$this->autoRender = false;

		if(in_array($_GET['passcode'], $this->authorizedPasscodes)) {
			echo json_encode(true);
		} else {
			echo json_encode(false);
		}
	}

}
