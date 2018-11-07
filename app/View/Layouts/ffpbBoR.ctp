<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version());

$currentGwData = json_decode(file_get_contents('https://fantasy.premierleague.com/drf/entry/300023'));
$currentGameweek = $currentGwData->entry->current_event;
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $this->fetch('title'); ?>
	</title>
	
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/b-1.4.2/b-flash-1.4.2/b-html5-1.4.2/cr-1.4.1/fc-3.2.3/fh-3.1.3/kt-2.3.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/sl-1.2.3/datatables.min.css"/>
 

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Latest compiled JavaScript -->
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/b-1.4.2/b-flash-1.4.2/b-html5-1.4.2/cr-1.4.1/fc-3.2.3/fh-3.1.3/kt-2.3.2/r-2.2.0/rg-1.0.2/rr-1.2.3/sc-1.4.3/sl-1.2.3/datatables.min.js"></script>
	<?php
		echo $this->Html->meta('icon');
// echo $this->Html->css('cake.generic');
		echo $this->Html->css('select2.min.css'); 
		echo $this->Html->css('./fontawesome/css/all.min.css');
		echo $this->Html->css('./fontawesome/css/fontawesome.min.css');
		echo $this->Html->css('jquery-confirm.min');  

		// echo $this->Html->script('jquery-3.1.1.min.js'); 
		echo $this->Html->script('select2.full.min.js');
		echo $this->Html->script('jquery-confirm.min.js');
		echo $this->Html->script('fontawesome.min.js');

		echo $this->fetch('meta');
		echo $this->fetch('css');
	?>
	
	<script type="text/javascript">
		const myBaseUrl = '<?php echo $this->base; ?>';
		const jsVars = '<?php echo json_encode($jsVars); ?>';
	</script>
	<?php
		echo $this->fetch('script');
	?>
</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">FFPB Battle of Royals</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="<?php echo (!empty($this->params['controller']) && !empty($this->params['action']) && ($this->params['controller']=='ffpbTeams') && ($this->params['action']=='viewTeams') )?'active' :'inactive' ?>">
				<?php echo $this->Html->link('Group List', '/ffpbTeams/viewTeams');?>
			</li>
			<li class="<?php echo (!empty($this->params['controller']) && !empty($this->params['action']) && ($this->params['controller']=='ffpbTeams') && ($this->params['action']=='pointTables') )?'active' :'inactive' ?>">
				<?php echo $this->Html->link('Point Table', '/ffpbTeams/pointTables');?>
			</li>
			<li class="<?php echo (!empty($this->params['controller']) && !empty($this->params['action']) && ($this->params['controller']=='ffpbMatches') && ($this->params['action']=='showLiveResults') )?'active' :'inactive' ?>">
				<?php echo $this->Html->link('Live Points', '/ffpbMatches/showLiveResults');?>
			</li>
			<li class="<?php echo (!empty($this->params['controller']) && !empty($this->params['action']) && ($this->params['controller']=='ffpbMatches') && ($this->params['action']=='showFixtures') )?'active' :'inactive' ?>">
				<?php echo $this->Html->link('Fixtures', '/ffpbMatches/showFixtures/'.$currentGameweek);?>
			</li>
		</ul>
		</div>
	</nav>
	<div id="container">
		<div id="content">

			<?php echo $this->Flash->render(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php
				echo $this->Html->script('ffpbBoR.js');  
			?>
		</div>
	</div>
</body>
</html>
