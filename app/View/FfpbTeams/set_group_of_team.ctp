<head>
	<title>
		Update Team
	</title>
</head>

<?php
 ?>



<body style="margin: 50px;">
	<?php echo $this->Session->flash(); //debug(array_key_exists('id', $this->request->query));?>
	
	<div class="col-xs-6" style="margin:0 auto;">
		<h3>Set group and sub-group of the team</h3>
		<div style="margin:0 auto;">
		<?php
		echo $this->Form->create('FfpbTeam', array('url' => 'setGroupOfTeam'));
		?>
		<div style="margin:10px;">
			<?php echo $this->Form->input('id', array(
			    'options' => $teamNames,
			    'empty' => 'choose one',
			    'label' => 'Select Team: ',
				'required' => 'required',
			    'value' => array_key_exists('id', $this->request->query)? $this->request->query['id']: ''
			));
			?>
		</div>

		<div style="margin:10px;">
			<?php 
				echo $this->Form->input('group_id', array(
					'options' => array('1' => 'A', '2' => 'B'),
					'empty' => 'choose one',
					'label' => 'Select Group: ',
					'required' => 'required',
					'value' => array_key_exists('group_id', $this->request->query)? $this->request->query['group_id']: ''
				));
				?>	
		</div>

		<div style="margin:10px;">
			<?php
			$options = array(0, 1, 2, 3, 4, 5, 6, 7, 8);
			unset($options[0]);
			echo $this->Form->input('subgroup_id', array(
			    'options' => $options,
			    'empty' => 'choose one',
			    'label' => 'Select Sub-Group: ',
				'required' => 'required',
			    'value' => array_key_exists('subgroup_id', $this->request->query)? $this->request->query['subgroup_id']: ''
			));
			?>
		</div>

		<div style="margin:10px;">
			<?php
			$options = array(0, 1, 2, 3, 4);
			unset($options[0]);
			echo $this->Form->input('subgroup_entry_position', array(
			    'options' => $options,
			    'empty' => 'choose one',
			    'label' => 'Select Sub-Group Entry Positon: ',
				'required' => 'required',
			    'value' => array_key_exists('subgroup_entry_position', $this->request->query)? ($this->request->query['subgroup_entry_position'] + 1)%5 : ''
			));
			?>
		</div>
		
		<?php
		echo $this->Form->submit('Update'); 
		echo $this->Form->end(); 
		?>
		</div>
	</div>
	
	<div class="col-xs-6" style="margin:0 auto;">
		<h2>Group List</h2>
		<?php
		foreach ($teamsByGroup as $group => $subGroups ) {
			echo '<div class="col-xs-6"><h3>Group ' . $group. '</h3>';
			foreach ($subGroups as $subGroupNo => $subGroup) {
				echo '<h4>Sub-Group ' . $subGroupNo . '</h4>';
				echo '<ol>';
				foreach ($subGroup as $entryPosition => $team) {
					echo '<li value="' . $entryPosition . '" style= "margin-left:10px;">' . $team['FfpbTeam']['team_name'] . '<i class="fa fa-plus-circle btn" aria-hidden="true"></i><ol style="display:none">';
					foreach ($team['FfpbPlayer'] as $player){
						$playerFplTeamLink = 'https://fantasy.premierleague.com/a/team/' . $player['player_code'] . '/event/' . $team['FfpbTeam']['current_gameweek'];
						?>
						<li>
							<a class="btm-md btn-primary" style="padding:1px;" href="<?php echo $player['fb_id'] ?>"> <?php echo $player['player_name'] ?> </a>
							<a class="btm-md btn-danger" style="margin-left:5px; padding:1px;" href="<?php echo $playerFplTeamLink ?>"> FPL link </a>
						</li>
						<?php
					}
					echo '</li></ol>';	
					
				}
				echo '</ol>';
			}
			echo '</div>';
			
		}
		?>
	</div>	
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<?php //echo $this->Html->script('battle_of_royals_results.js'); ?>