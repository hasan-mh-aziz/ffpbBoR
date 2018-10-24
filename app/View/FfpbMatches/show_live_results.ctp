<head>
	<title>
		Update Team
	</title>
</head>

<?php
 ?>



<body style="margin: 50px;">
	<div id="ajaxLoaderDiv" style="
		display:	none;
	    z-index:    1000;
	    top:        0;
	    left:       0;
	    height:     100%;
	    width:      100%;
	    background: rgba( 255, 255, 255, .8 ) 
	                url('img/ajax-loader.gif') 
	                50% 50% 
	                no-repeat;"
    >
    	Loading
	</div>
	<h1 style="width:550px; margin:0 auto;">FFPB Battle of Royals season 2</h1>
	<h2 style="margin-top:30px; width:200px; margin:0 auto;">Matches List</h2>
	<h3 style="margin-top:30px; width:200px; margin:0 auto;" id="gameweekShow"></h3>
	<h4>As it collects data from main Fpl site for each individual player, it will take a minute to collect the data. So, please be patience for a while.</h4>
	<?php echo $this->Session->flash();?>

	<div class="table">
		<table id="liveScoreSheet" style="width:95%" class="table fixtureTable">
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
	
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<?php echo $this->Html->script('showLiveResults.js'); ?>