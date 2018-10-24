<?php

App::uses('AppModel', 'Model');
class FfpbPlayerInMatch extends AppModel {
    public $name = 'ffpb_player_in_match';

    public $belongsTo = array(
        'team' =>
            array(
                'className' => 'FfpbMatch',
                // 'joinTable' => 'ingredients_recipes',
                'foreignKey' => 'match_id',
                'unique' => false,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            ),
        'player' =>
            array(
                'className' => 'FfpbPlayer',
                // 'joinTable' => 'ingredients_recipes',
                'foreignKey' => false,
	             'conditions' => array(
	                 'player.player_id = FfpbPlayerInMatch.player_id'
	             ),
                'unique' => false,
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            )
    );
}