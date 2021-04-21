<?php
require('database_connect.php');

$reqStops = "SELECT * FROM `transport_stops` ORDER BY name";
$startStops = $dbh->query($reqStops);
$endStops = $dbh->query($reqStops);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="author" content="Audrey DENOUAL">
	<meta name="description" content="Hyperloop">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Hyperloop</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="container">
		<h1 class="m-0">Select your journey</h1>
	</div>
	<div class="card-body">
		<div class="row">
			<label class="col-md-1" for="startSelect">Start</label>
			<select id="startSelect" class="form-control col-md-2" name="startSelect" onChange="fields_valid()">
				<option value="empty"></option>
				<?php
				foreach ($startStops->fetchAll() as $stop) {
					$id = $stop["id"];
					$name = $stop["name"];
					echo "<option value='$id'>$name</option>";
				} ?>
			</select>

			<label class="col-md-1 offset-md-1" for="endSelect">End</label>
			<select id="endSelect" class="form-control col-md-2" name="endSelect" onChange="fields_valid()">
				<option value="empty"></option>
				<?php
				foreach ($endStops->fetchAll() as $stop) {
					$id = $stop["id"];
					$name = $stop["name"];
					echo "<option value='$id'>$name</option>";
				} ?>
			</select>
			<div class="col-md-2 offset-md-1">
				<button id="searchButton" class="btn btn-primary" type="submit" onClick="searchJourney()" disabled>Search</button>
			</div>
		</div>
	</div>

	<div id="result">
	</div>

	<script type="text/javascript">
    	function fields_valid() {
				var startSelect = document.getElementById('startSelect');
				var start = startSelect.options[startSelect.selectedIndex].value;

				var endSelect = document.getElementById('endSelect');
				var end = endSelect.options[endSelect.selectedIndex].value;

				if (start!='empty' && end!='empty') {
					document.getElementById('searchButton').disabled = false;
				} else {
					document.getElementById('searchButton').disabled = true;
				}
			}

		function searchJourney() {
			start = $("#startSelect").val();
			end = $("#endSelect").val();
			console.log(end);
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("result").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET","test.php?start="+start+"&end="+end,true);
			xmlhttp.send();
		}
	</script>
