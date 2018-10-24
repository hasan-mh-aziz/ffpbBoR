<head>
	<title>
		Update Team
	</title>
</head>

<?php
 ?>



<body style="margin: 50px;">
	<?php echo $this->Session->flash();?>
	<h3>Set fixtures</h3>
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

<?php echo $this->Html->script('setUpFixtures.js'); ?>