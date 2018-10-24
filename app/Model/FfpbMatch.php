<?php

App::uses('AppModel', 'Model');
class FfpbMatch extends AppModel {
    public $name = 'ffpb_matches';

    public $belongsTo = array(
        'entry1' =>
            array(
                'className' => 'FfpbTeam',
                // 'joinTable' => 'ingredients_recipes',
                'foreignKey' => 'entry1',
                'unique' => false,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            ),
        'entry2' =>
            array(
                'className' => 'FfpbTeam',
                // 'joinTable' => 'ingredients_recipes',
                'foreignKey' => 'entry2',
                'unique' => false,
                'conditions' => '',
                'fields' => '',
                'order' => '',
                'limit' => '',
                'offset' => '',
                'finderQuery' => '',
                'with' => ''
            )
    );

    public $hasMany = array(
        'playerInMatch' => array(
        	'className' => 'FfpbPlayerInMatch',
            'foreignKey' => 'match_id',
            'with' => 'FfpbPlayerInMatch',
        ),
    );

   
}