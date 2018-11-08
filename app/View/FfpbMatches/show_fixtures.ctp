<head>
	<title>
		Show Fixtures
	</title>
</head>

<?php
 ?>



<body style="margin: 50px;">
	<div id="ajaxLoaderDiv" style="
		position: absolute;
		display:	none;
	    z-index:    1000;
	    top:        0;
	    left:       0;
	    height:     100%;
	    width:      100%;
	    background: rgba( 255, 255, 255, .8 ) 
	                url(<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>)  
	                50% 50% 
	                no-repeat;"
    >
    	Loading
	</div>
	<h1 style="width:550px; margin:0 auto;">FFPB Battle of Royals season 3</h1>
	<h2 style="margin-top:30px; width:200px; margin:0 auto;">Fixtures</h2>
	<h3 style="margin-top:30px; width:200px; margin:0 auto;" id="gameweekShow"></h3>

	<div class="row" style="margin-top: 40px;">
		<div class="col-xs-2">
			<button class="btn btn-primary" id="prevBtn" style="float: left;"><i class="fas fa-arrow-left"></i> Previous</button>
		</div>
		<div class="col-xs-offset-8 col-xs-2">
			<button class="btn btn-primary" id="nextBtn" style="float: right;">Next <i class="fas fa-arrow-right"></i></button>
		</div>
	</div>

	<div class="table" style="margin-top: 20px; ">
		<table id="fixtureByGw" style="width:95%" class="table fixtureTable">
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

<?php echo $this->Html->script('show_fixtures.js'); ?>