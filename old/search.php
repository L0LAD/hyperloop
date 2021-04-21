<?php

	$conn = new mysqli("localhost", "root", "", "hyperloop");
	if ($conn->connect_error) {
	    die("La connexion à la base de données a échoué: " . $conn->connect_error);
	}

	if (isset($_POST['startSelect']) && isset($_POST['endSelect'])) {
		$nameStart = $_POST['startSelect'];
		$nameEnd = $_POST['endSelect'];
		include('route.php');	
	}

?>

<!DOCTYPE html>
<html lang="en">

	<?php include('head.php'); ?>

  <body>

  	<?php include('header.php'); ?>

  	<div id="route-preloader">
		<h1></h1>
	</div>

	<div id="wrapper"> 

	  	<div class="container">
			<div class="card shadow mb-4">   <!-- Carte -->
	        	<div class="card-body">   <!-- Contenu -->
	        		<p><?php
		        		echo "<b>Il y a " .sizeof($possibilityList). " trajets possibles entre " .$nameStart. " et " .$nameEnd. ".</b><br>";
		        		echo "Ce trajet comporte " .$nbrStep. " correspondances.<br>";
		        		echo "Il y a " .$nbrStop. " arrêts entre " .$nameStart. " et " .$nameEnd. ".<br>";
						echo "La distance parcourue est de " .$distance. "km, soit " .getDuration($distance). ".<br><br>";
					?></p>
	        	</div>
	        </div>
	    </div>
	</div>

    <!-- JavaScript optionnel -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>

</html>