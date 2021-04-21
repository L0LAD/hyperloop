<?php

	//Informations de la station de départ
		$idStart = getIDStop($conn, $nameStart);
		$infoStart = getInfoStop($conn, $idStart);
		$statusStart = $infoStart['status'];

	//Informations de la station d'arrivée
		$idEnd = getIDStop($conn, $nameEnd);
		$infoEnd = getInfoStop($conn, $idEnd);
		$statusEnd = $infoEnd['status'];

	//Vérification de la validité de l'itinéraire
		if ($idStart!=$idEnd && $statusStart=='open' && $statusEnd=='open') {
			$reachable = true;
		} else {
			$reachable = false;
		}	

	//Recherche de tous les itinéraires  possibles
		$possibilityList = array();
		$route = array();
		$usedLine = array();
		$usedHub = array();
		$waitingLine = array();
		$waitingHub = array();
		recursive($conn, $idStart, $idEnd);
		if ($reachable == true && !empty($possibilityList)) {	//Calcul des informations sur les trajets si existants
			foreach ($possibilityList as $route) {				//Sélection d'un itinéraire potentiel
				$idStopA = $idStart;
				$idStopB = 0;
				$nbrStep = sizeof($route) - 1;
				$nbrStop = $nbrStep;
				$distance = 0;
				foreach ($route as $step) {						//Sélection d'une étape du trajet
					if ($idStopB) {
						$idStopA = $idStopB;
					}
					$idStopB = $step['end'];
					$stepInfo = getStep($conn, $step['line'], $idStopA, $idStopB);
					$nbrStop += $stepInfo['nbrStop'];
					$distance += $stepInfo['distance'];
				}
			}
		} else {
			echo "Ce trajet n'est pas disponible.";
		}

//Fonctions
	//ID d'une station
		function getIDStop ($conn, $name) {
			$reqID = "SELECT id FROM stop WHERE name='$name'";
			$ansID = $conn->query($reqID);
			$id = mysqli_fetch_array($ansID)[0];
			return $id;
		}

	//Informations sur une station
		function getInfoStop ($conn, $id) {
			$reqInfo = "SELECT * FROM stop WHERE id='$id'";
			$ansInfo = $conn->query($reqInfo);
			if ($ansInfo->num_rows > 0) {
				while ($stop = $ansInfo->fetch_assoc()) {
					$info = ['name' => $stop['name'], 'status' => $stop['status'], 'line' => getLine($stop)];
				}
			}
			return $info;
		}

	//Liste des lignes passant dans une station
		function getLine ($stop) {
			$nbrLine = 5;
			$line = 1;
			$lineList = array();
			while ($line <= $nbrLine) {
				if ($stop[$line] != '0') {
					array_push($lineList, $line);
				}
				$line++;
			}
			return $lineList;
		}

	//Comparaison de listes de lignes
		function compareLine ($lineStart, $lineEnd) {
			$possibleWayList = array();
			foreach($lineStart as $lineA){
			    foreach($lineEnd as $lineB) {
			    	if ($lineA == $lineB) {
			    		array_push($possibleWayList, $lineA);
			    	}
			    }
			}
			return $possibleWayList;
		}

	//Liste des hubs sur une ligne
		function getHub ($conn, $line) {
			$reqHub = "SELECT * FROM network JOIN stop ON id=id_stop WHERE id_line=$line AND hub!='0' AND status='open'";
			$ansHub = $conn->query($reqHub);
			return $ansHub;
		}

	//Recherche des trajets possibles
		function allWay () {
			
		}

	//Recherche d'un trajet
		function recursive ($conn, $idA, $idB) {
			global $route;
			global $possibilityList;
			global $usedLine;
			global $usedHub;
			global $waitingLine;
			global $waitingHub;
			$infoA = getInfoStop($conn, $idA);
			$lineA = $infoA['line'];
			$infoB = getInfoStop($conn, $idB);
			$lineB = $infoB['line'];		
			$compatibleLineList = compareLine($lineA,$lineB);
			if ($compatibleLineList) {
				foreach ($compatibleLineList as $line) {
					$route[] = ['start' => $idA, 'line' => $line, 'end' => $idB];
					$possibilityList[] = $route;
					if ($line == end($compatibleLineList)) {
						$route = array();
					} else {
						array_pop($route);
					}
				}
				if (!empty($waitingLine)) {
					/*echo "<br>Some lines are still waiting...";
					print_r($waitingLine);*/
				}
			} else {
				foreach ($lineA as $line) {
					if (!in_array($line, $usedLine)) {
						array_push($usedLine, $line);
						if ($line != end($lineA)) {
							$lineA = array_slice($lineA, 1);
							$waitingLine[] = [$idA, $lineA];
							/*echo "<br>WAITING LINE : ";
							print_r($waitingLine);*/
						}
						$hubList = getHub($conn, $line);
						foreach ($hubList as $hub) {
							if (!in_array($hub['id'], $usedHub)) {
								array_push($usedHub, $hub['id']);

								/*if ($hub != end($hubList)) {
									foreach ($hubList as $otherHub) {
										if ($otherHub != $hub) {
											$waitingHub[] = [$line, $otherHub['id']];
										}
									}
									echo "<br>WAITING HUB : ";
									print_r($waitingHub);
								}*/

								if ($route && end($route)['start'] == $idA) {
									array_pop($route);
									$route[] = ['start' => $idA, 'line' => $line, 'end' => $hub['id']];
									recursive ($conn, $hub['id'], $idB);
								} else {
									$route[] = ['start' => $idA, 'line' => $line, 'end' => $hub['id']];
									recursive ($conn, $hub['id'], $idB);
								}
							}
						}
					}
				}
			}
		}

	//Rang d'une station sur une ligne
		function getRank ($conn, $line, $stop) {
			$reqRank = "SELECT rank FROM network WHERE id_stop=$stop AND id_line=$line";
			$ansRank = $conn->query($reqRank);
			$rank = mysqli_fetch_array($ansRank)[0];
			return $rank;
		}

	//Liste des arrêts à parcourir sur une ligne en fonction du sens
		function getWay($conn, $line, $rankStart, $rankEnd) {
			$reqStop = "";
			if ($rankEnd > $rankStart) {
					$way = "UP";
					$reqStop = "SELECT name,status,hub,rank,distance FROM network JOIN stop ON id_stop=id WHERE rank>$rankStart AND rank<=$rankEnd AND id_line=$line ORDER BY rank";
			} else {
				$way = "DOWN";
				$reqStop = "SELECT name,status,hub,rank,distance FROM network JOIN stop ON id_stop=id WHERE rank>$rankEnd AND rank<=$rankStart AND id_line=$line ORDER BY rank DESC";
			}
			$ansStop = $conn->query($reqStop);
			return $ansStop;
		}

	//Calcul de l'itinéraire sur une étape
		function getStep($conn, $line, $idStart, $idEnd) {
			$rankStart = getRank($conn, $line, $idStart);
			$rankEnd = getRank($conn, $line, $idEnd);
			$ansStop = getWay($conn, $line, $rankStart, $rankEnd);	//Liste des arrêts sur une ligne entre 2 points
			$distance = 0;
			$nbrStop = -1;
			if ($ansStop->num_rows > 0) {
				while ($stop = $ansStop->fetch_assoc()) {
					$distance += $stop['distance'];
					if ($stop['status'] == 'open') {
						$nbrStop++ ;
					}
				}
			}
			$step = ['nbrStop' => $nbrStop, 'distance' => $distance];
			return $step;
		}

	//Conversion minutes-heures
		function getDuration ($distance) {
			$duration;
			$speed = 1050;
			$min = round($distance/$speed*60 + 10,0);
			$hour = 0;
			while ($min >=60) {
				$min -= 60;
				$hour++;
			}
			if ($hour == 0) {
				$duration = $min. 'mn';
			} else if ($min < 10) {
				$duration = $hour. 'h0' .$min;
			} else {
				$duration = $hour. 'h' .$min;
			}
			return $duration;
		}

?>