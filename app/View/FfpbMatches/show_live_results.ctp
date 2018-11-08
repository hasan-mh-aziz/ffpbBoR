<head>
	<title>
		Update Team
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
	<h2 style="margin-top:30px; width:200px; margin:0 auto;">Matches List</h2>
	<h3 style="margin-top:30px; width:200px; margin:0 auto;" id="gameweekShow"></h3>
	<h4>As it collects data from main Fpl site for each individual player, it will take a minute to collect the data. So, please be patience for a while.</h4>
	<?php echo $this->Session->flash();?>

	<div class="row" style="margin-top: 40px;">
		<div class="col-xs-offset-2 col-xs-3">
				<?php 
					echo $this->Form->input('group_id', array(
						'options' => array('1' => '1', '2' => '2'),
						'empty' => 'choose one',
						'label' => 'Select Group: ',
						'required' => 'required',
						'value' => array_key_exists('group_id', $this->request->query)? $this->request->query['group_id']: ''
					));
					?>	
		</div>

		<div class="col-xs-3">
			<?php
			$options = array_combine(range('A', 'M'), range('A', 'M'));
			echo $this->Form->input('subgroup_id', array(
			    'options' => $options,
			    'empty' => 'choose one',
			    'label' => 'Select Sub-Group: ',
				'required' => 'required',
			    'value' => array_key_exists('subgroup_id', $this->request->query)? $this->request->query['subgroup_id']: ''
			));
			?>
		</div>
		<div class="col-xs-2">
			<button class="btn btn-info" id="showBtn">Show</button>
		</div>
	</div>

	<div class="table" style="margin-top: 20px; ">
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