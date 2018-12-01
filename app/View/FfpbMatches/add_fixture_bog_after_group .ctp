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
	                url(<?php echo $this->webroot . 'img/ajax-loader.gif'; ?>) 
	                50% 50% 
	                no-repeat;"
    >
    	Loading
	</div>
	<h1 style="width:550px; margin:0 auto;">FFPB Battle of Royals season 2</h1>
	<h2 style="margin-top:30px; width:200px; margin:0 auto;">Matches List</h2>
	<h3 style="margin-top:30px; width:200px; margin:0 auto;" id="gameweekShow"></h3>

	
	<?php
		echo $this->Form->create('FfpbMatch', array('url' => 'setUpFixtures'));
		?>
		<div style="margin:10px;">
			<?php echo $this->Form->input('gameweek', array(
			    'type' => 'number',
			    'empty' => 'choose one',
			    'label' => 'Eenter Gameweek: '
			));
			?>
		</div>

		<div style="margin:10px;">
			<?php
			$options = array(0, 1, 2, 3, 4);
			unset($options[0]);
			echo $this->Form->input('entry1SubgroupPostion', array(
			    'options' => $options,
			    'empty' => 'choose one',
			    'label' => 'Select Sub-Group Entry Positon for Team 1: '
			));
			?>
		</div>

		<div style="margin:10px;">
			<?php
			$options = array(0, 1, 2, 3, 4);
			unset($options[0]);
			echo $this->Form->input('entry2SubgroupPostion', array(
			    'options' => $options,
			    'empty' => 'choose one',
			    'label' => 'Select Sub-Group Entry Positon for Team 2: '
			));
			?>
		</div>		
		<?php
		echo $this->Form->submit('Enter'); 
		echo $this->Form->end(); 
		?>	
</body>

<!-- <script type="text/javascript" src="js/iccfpl_result.js"></script> -->

<!-- <?php echo $this->Html->script('update.js?v='.$jsVersion); ?> -->