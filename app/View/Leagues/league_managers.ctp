<head>
	<title>
		FPL League Extractor
	</title>
</head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-2.2.3/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-flash-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.css"/>
<link rel="stylesheet" type="text/css" href="css/select2.min.css"/>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-2.2.3/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/af-2.1.2/b-1.2.2/b-colvis-1.2.2/b-flash-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/fc-3.2.2/fh-3.1.2/kt-2.1.3/r-2.1.0/rr-1.1.2/sc-1.4.2/se-1.2.0/datatables.min.js"></script>
<script type="text/javascript" src="js/select2.full.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


<body style="margin: 50px;">
	<div>
		<label>Enter the number of the gameweek for link to the point(default 1) :</label>
		<div style="clear:both;"></div> 
		<input type="number" id="gameWeekInput">
	</div>
	<div style="margin-top:30px;">
		<label>Give league ID or select a league :</label>
		<div style="clear:both;"></div> 
		<input type="number" class="leagueIDInput">
		<button class="searchButton">Search</button>
		<span style="margin-left:30px;margin-right:30px;">OR</span>
		<select class="leagueIDInput convert2select2">
			<option></option>
			<option value="1120">7TEEN FFPB Fantasy League</option>
			<option value="131514">Football FrEaK Cadetzzzzzz</option>
			<option value="829515">Mirzapurian 42nd</option>
			<option value="177011">FFBD Classic League</option>
			<option value="527582">Plaantik</option>860561
			<option value="149708">The Etojoss League</option>
			<option value="860561">MCC Ex-Cadets League</option>
		</select>
		<button class="searchButton">Search</button>
	</div>

	<div style="margin-top: 50px;">

		<table id="playerListTable">
			<thead>
				<tr>
					<td>#GW Rank</td>
					<td style="width:20%">Player Name</td>
					<td>Team Name</td>
					<td>GW Point</td>
					<td>Total Ponit</td>
					<td>Rank</td>
					<td style="width:35%">Team link</td>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</body>

<?php echo $this->Html->script('league_managers'); ?>