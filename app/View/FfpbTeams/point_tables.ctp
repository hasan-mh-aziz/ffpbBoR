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
		<div id="ajaxLoaderDiv" style="
		display:	none;
		text-align: center;
		vertical-align: middle
	    z-index:    1000;
	    top:        0;
	    left:       0;
	    height:     100%;
	    width:      100%;
	    position: absolute;
	    background: rgba( 255, 255, 255, .8 )
	    		url(<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>)
	                50% 50% 
	                no-repeat;"
    >
    </div>
	<div id="PointTable" class="tab-pane fade in active">
	    	<?php
      		foreach ($teamsByGroup as $group => $subGroups ) {
      			# code...
      		?>
	      	<div style="margin-top: 50px;">
      		<h3>Group <?php echo $group ?></h3>
	      		<?php
	      		foreach ($subGroups as $subGroup => $teams ) {
	      			# code...
	      		?>
	      		<div style="margin-top: 10px;">
	      			<legend>Subgroup <?php echo $group.$subGroup ?></legend>
					<table class="table" id="pointTable<?php echo $group.$subGroup ?>" class="pointTableDisplay" value="<?php echo $subGroup ?>">
						<thead>
							<tr>
								<td># Rank</td>
								<td>Team Name</td>
								<td>Played</td>
								<td>Win</td>
								<td>Draw</td>
								<td>Score For</td>
								<td>Score Against</td>
								<td>Score Difference</td>
								<td>Total Points</td>
								<td>BoR</td>
								<td>BoG</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 0;
				      		foreach ($teams as $index => $team ) {
				      		?>
				      		<tr>
					      		<td><?php echo ++$count; ?></td>
					      		<td><?php echo $team['FfpbTeam']['team_name']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['played']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['win']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['draw']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['score_for']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['score_against']; ?></td>
					      		<td><?php echo  $team['FfpbTeam']['score_for'] - $team['FfpbTeam']['score_against']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['win']*3 + $team['FfpbTeam']['draw']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['inBoR']; ?></td>
					      		<td><?php echo $team['FfpbTeam']['inBoG']; ?></td>
				      		</tr>
				      		<?php }?>	
						</tbody>
					</table>
	      		</div>
		      	<?php }?>	
			</div>

			<?php }?>
	    </div>
			
	</div>	
</body>

<!-- <script type="text/javascript" src="js/ffpb_point_table.js"></script> -->