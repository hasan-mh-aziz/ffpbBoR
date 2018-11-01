<?php


App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel');
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel'.DS.'IOFactory.php'));


class FfpbHitCountControlInGwsController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('FfpbHitCountControlInGw');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */

	public function getHitCountGw($gameweek = 10) {
		$this->autolayout = false;
		$this->autoRender = false;

		$hitCountControlInGw = $this->FfpbHitCountControlInGw->find('all',
			array(
				'conditions' => array('FfpbHitCountControlInGw.gw' => $gameweek),
				'recursive' => 0,
				));
	    echo json_encode($hitCountControlInGw);


	}


}
