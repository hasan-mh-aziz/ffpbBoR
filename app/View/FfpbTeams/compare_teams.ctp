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
		<h1 style="width:550px; margin:0 auto;">FFPB Battle of Royals season 2</h1>
		<h2 style="width:400px; margin:0 auto;">Compare Teams</h2>
		<div class="col-xs-12" style="margin-top:50px;" >
			<div class="col-xs-5">
				<?php echo $this->Form->input('team1_id', array(
				    'options' => $teamNames,
				    'empty' => 'choose one',
				    'label' => 'Select Team: ',
					'required' => 'required'
				));
				?>
			</div>
			<div class="col-xs-5">
				<?php echo $this->Form->input('team2_id', array(
				    'options' => $teamNames,
				    'empty' => 'choose one',
				    'label' => 'Select Team: ',
					'required' => 'required'
				));
				?>
			</div>
			<div class="col-xs-2">
				<button class="btn btn-primary" id="compareBtn">Compare</button>
			</div>
		</div>

		<div id="teamComparasionDiv">
			
		</div>
			
	</div>	
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<?php echo $this->Html->script('compareTeams.js'); ?>