<?php

	$conn = new mysqli("localhost", "root", "", "hyperloop");
	if ($conn->connect_error) {
	    die("La connexion à la base de données a échoué: " . $conn->connect_error);
	}

	$reqNameStop = "SELECT * FROM stop ORDER BY name";
	$ansNameStart = $conn->query($reqNameStop);
	$ansNameEnd = $conn->query($reqNameStop);
	
?>

<!DOCTYPE html>
<html lang="en">

	<?php include('head.php'); ?>

  <body>
  	<div id="index-preloader">
    </div>

  	<div id="wrapper"> 
  		
  		<?php include("header.php") ?>

	  	<div class="container">
			<div class="card shadow mb-4">   <!-- Carte -->
	    		<div class="card-header py-3">   <!-- Titre -->
	          		<h1 class="m-0">Select your journey</h1>
	        	</div>
	        	<div class="card-body">   <!-- Contenu -->
	        	 	<form method="post" action="../hyperloop/search.php">
						<div class="row">
		              		<label class="col-md-1">Start</label>
							<select id="startSelect" class="form-control col-md-2" name="startSelect" onChange="fields_valid()">
								<option value="empty"></option>
								<?php
								while ($stop = $ansNameStart->fetch_assoc()) {
									$name = $stop["name"];
									echo "<option value='$name'>$name</option>";
								} ?>
							</select>
							<label class="col-md-1 offset-md-1">End</label>
							<select id="endSelect" class="form-control col-md-2" name="endSelect" onChange="fields_valid()">
								<option value="empty"></option>
								<?php
								while ($stop = $ansNameEnd->fetch_assoc()) {
									$name = $stop["name"];
									echo "<option value='$name'>$name</option>";
								} ?>
							</select>
							<div class="col-md-2 offset-md-1"><button id="searchButton" class="btn btn-primary" type="submit" disabled>Search</button></div>
			        	</div>
					</form>
	        	</div>
	        </div>    
		</div>
	</div>

	<!-- JavaScript -->
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
	</script>

    <!-- JavaScript optionnel -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>

</html>