<?php
	session_start();
	@ini_set('display_errors',1);
	include_once "../config/define.php";
	include_once "../include/db.php";
	
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/svg.js/2.7.1/svg.min.js" crossorigin="anonymous"></script>
		<title>Hello, world!</title>
	</head>
	<body>
		<h1>Harvest Box Monitoring By Farm Maniac</h1>
		<div class="jumbotron jumbotron-fluid">
			<div class="container">
				<h1 class="display-4">Machine ID : HBOX00001</h1>
				<a href="index.php" type="button" class="btn btn-primary">Live Data</a>
				<a href="100.php" type="button" class="btn btn-secondary">Lastest 100 Record</a>
				<a href="avg.php" type="button" class="btn btn-success">Overall Average</a>
				<a href="report.php" type="button" class="btn btn-danger">Report</a>
			</div>
		</div>
		<table class="table table=-bordered table-striped table-sm" >
			<thead>
				<tr>
					<th class="text-center">Date</th>
					<th class="text-center">Time</th>
					<th class="text-center">Humidity</th>
					<th class="text-center">Tempurature</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$sql = "SELECT * FROM farmedges_records ORDER BY created DESC LIMIT 0,100";
			$rst = $dbc->Query($sql);
			while($record = $dbc->Fetch($rst)){
				echo '<tr>';
					echo '<td class="text-center">'.date("d F Y",strtotime($record['created'])).'</td>';
					echo '<td class="text-center">'.date("H:i:s",strtotime($record['created'])).'</td>';
					echo '<td class="text-center">'.$record['humidity'].' %</td>';
					echo '<td class="text-center">'.$record['temp'].' °C</td>';
				echo '</tr>';
				
			}
			?>
			</tbody>
		</table>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>

<?php
	$dbc->Close();
?>