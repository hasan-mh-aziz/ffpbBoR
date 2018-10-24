<head>
	<title>
		Update Team
	</title>
</head>

<?php
 ?>



<body style="margin: 50px;">
	<?php echo $this->Session->flash(); //debug(array_key_exists('id', $this->request->query));?>
	
	
	<div class="container">
		<h1 style="width:550px; margin:0 auto;">FFPB Battle of Royals season 2</h1>
		<h2 style="margin-top:30px; width:200px; margin:0 auto;">Group List</h2>
		<?php
		foreach ($teamsByGroup as $group => $subGroups ) {
			echo '<div class="col-xs-6" style="margin-top:30px;"><h3 style="width:200px; margin:0 auto;">Group ' . $group. '</h3><ul style="margin-top:30px; columns: auto auto;">';
			foreach ($subGroups as $subGroupNo => $subGroup) {
				echo '<li><h4>Sub-Group ' . $subGroupNo . '</h4>';
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
				echo '</ol></li>';
			}
			echo '</ul></div>';
			
		}
		?>
	</div>	
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<?php //echo $this->Html->script('battle_of_royals_results.js'); ?>