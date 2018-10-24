<head>
	<title>
		ICCFPL
	</title>
</head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-2.2.3/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-flash-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.css"/>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-2.2.3/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-flash-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php
 echo $this->Html->script('select2.full.min.js'); 
 echo $this->Html->css('select2.min.css'); 
 echo $this->Html->css('font-awesome.min'); 
 ?>



<body style="margin: 50px;">
	<div class="container">
	  <ul class="nav nav-tabs">
	    <li class="active"><a data-toggle="tab" href="#PointTable">Point Table</a></li>
	    <li id="liveScoreTab"><a data-toggle="tab" href="#gwPoints">Live Scores</a></li>
	    <li id="allFixtureTab"><a data-toggle="tab" href="#fixturesBlock">Fixtures</a></li>
	    <li><a data-toggle="tab" href="#menu3">Menu 3</a></li>
	  </ul>

	  <div class="tab-content">
	    <div id="PointTable" class="tab-pane fade in active">
	    	<?php
      		foreach ($subGroups as $key => $subGroup) {
      			# code...
      		?>
	      	<div style="margin-top: 50px;">
	      		<!-- <button class="click">Update Point Table</button> -->
      		
	      		<legend>Group <?php echo $subGroup['ffpb_subgroups']['subgroup_name'] ?></legend>
				<table id="pointTable<?php echo $subGroup['ffpb_subgroups']['subgroup_name'] ?>" class="pointTableDisplay" value="<?php echo $subGroup['ffpb_subgroups']['id'] ?>">
					<thead>
						<tr>
							<td># Rank</td>
							<td>Team Name</td>
							<td>Played</td>
							<td>Win</td>
							<td>Lost</td>
							<td>Score For</td>
							<td>Score Against</td>
							<td>Score Difference</td>
							<td>Total Points</td>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>

			<?php }?>

			<!-- <div style="margin-top: 50px;">
				<legend>Group B</legend>
				<table id="pointTableB">
					<thead>
						<tr>
							<td># Rank</td>
							<td>Team Name</td>
							<td>Played</td>
							<td>Win</td>
							<td>Lost</td>
							<td>Score For</td>
							<td>Score Against</td>
							<td>Score Difference</td>
							<td>Total Points</td>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div> -->
	    </div>

	    <div id="gwPoints" class="tab-pane fade">
	      <div style="margin-top: 50px;">
				<legend>Scores for Gameweek <span id="currentGwLive"><?php echo $actualCurrentGw; ?></span></legend>
				<table id="LiveScoreSheet" style="width:95%" class="scoreTable">
					<thead>
						<tr>
							<td></td>
							<td>Team1 Name</td>
							<td>Team1 Scores</td>
							<td><span style="margin-left:0px;">Sub Group</span></td>
							<td>Team2 Scores</td>
							<td>Team2 Name</td>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			
	    </div>
	    <div id="fixturesBlock" class="tab-pane fade">
		    <div style="margin-top: 50px;">
	    		<button type="button" class="btn btn-md btn-default changeFixtureButton" id="previousGWFixture" value="-1"><i class="fa fa-long-arrow-left" aria-hidden="true"> Previuos</i></button>
	    		<button type="button" class="btn btn-md btn-default changeFixtureButton pull-right" id="previousGWFixture" value="1">Next <i class="fa fa-long-arrow-right" aria-hidden="true"> </i></button>

		    	<legend>Scores for Gameweek <span id="currentFixtureGw"><?php echo $actualCurrentGw; ?></span></legend>
	      		<table id="fixturesTable" style="width:95%" class="scoreTable table-striped">
					<thead>
						<tr>
							<td></td>
							<td>Team1 Name</td>
							<td>Team1 Scores</td>
							<td><span style="margin-left:0px;">Sub Group</span></td>
							<td>Team2 Scores</td>
							<td>Team2 Name</td>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
	    </div>
	    <div id="menu3" class="tab-pane fade">
	      <h3>Menu 3</h3>
	      <p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
	    </div>
	  </div>
	</div>

		
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<?php echo $this->Html->script('battle_of_royals_results.js'); ?>