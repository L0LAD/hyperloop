<?php

require('database_connect.php');

$step = 0;
$possibleRoutes = [];
$previousHubs = [0];

$start = $_GET['start'];
$end = $_GET['end'];
recursive($start, $end, []);
echo "De  ".getStopName($start)." à  ".getStopName($end)."<br />";

$resumeRoutes = [];

foreach ($possibleRoutes as $key => $route) {
	$steps = sizeof($route);
	$totalDistance = 0;
	foreach ($route as $segment) {
		$totalDistance += $segment['distance'];
	}
	$resumeRoutes[$key] = ['id' => $key, 'distance' => round($totalDistance), 'steps' => $steps];
}

$sortedRoutes = array();
foreach($resumeRoutes as $route){
    foreach($route as $key => $value){
        if(!isset($sortedRoutes[$key])){
            $sortedRoutes[$key] = array();
        }
        $sortedRoutes[$key][] = $value;
    }
}
$orderby = "distance";
array_multisort($sortedRoutes[$orderby],SORT_ASC,$resumeRoutes);

echo "Il y a ".sizeof($resumeRoutes)." trajets possibles.";

foreach ($resumeRoutes as $resume) {
	$distance = $resume['distance'];
	$steps = $resume['steps'] - 1;
	echo "<br />Ce trajet dure $distance km et comporte $steps correspondance :<br />";
	$route = $possibleRoutes[$resume['id']];
	for ($i = 0; $i < sizeof($route); $i++) {
		if ($route[$i] == end($route))
			$correspondance = getStopName($end);
		else
			$correspondance = getStopName($route[$i+1]['start']);
		$line = getLineName($route[$i]['line']);
		$lineName = $line['line']." ".$line['line_oriented'];
		echo "&nbsp;&nbsp;- Prendre $lineName jusqu'à $correspondance<br />";
	}
}

//var_dump($possibleRoutes);

/* ------------------------------------------------ */

function recursive ($start, $end, $currentRoute) {
	$step = $GLOBALS['step'];
	$possibleRoutes = $GLOBALS['possibleRoutes'];
	$previousHubs = $GLOBALS['previousHubs'];

	$linesStart = getLinesFromStop($start);
	$linesEnd = getLinesFromStop($end);
	$commonLines = compareLines($linesStart,$linesEnd);

	if (!empty($commonLines)) {
		foreach ($commonLines as $line) {
			$orientedLine = getLineOriented($start,$end,$line);
			$distance = getDistance($start,$end,$orientedLine);
			$currentSegment = ["start" => intval($start), "line" => intval($orientedLine), "distance" => $distance];
			if ($currentRoute && $currentSegment["line"] != end($currentRoute)["line"]) {
				array_push($currentRoute,$currentSegment);
				array_push($GLOBALS['possibleRoutes'],$currentRoute);
				array_pop($currentRoute);
			}
		}
	}
	//else {
		if (sizeof($currentRoute) < 2) {
			foreach ($linesStart as $line) {
				$stopsToAvoid = array_merge($previousHubs, [$start]);
				$hubs = getHubsFromLine($line,join(",",$stopsToAvoid));
				foreach ($hubs as $hub) {
					$hub = $hub['stop_id'];
					if (!in_array($hub, $previousHubs)) {
						array_push($GLOBALS['previousHubs'],intval($hub));
						$orientedLine = getLineOriented($start,$hub,$line);
						$lineNotUsed = true;
						foreach ($currentRoute as $segment) {
							if ($segment['line'] == $orientedLine) {
								$lineNotUsed = false;
								break;
							}
						}
						if ($lineNotUsed && $hub != $end) {
							$distance = getDistance($start,$hub,$orientedLine);
							$currentSegment = ["start" => intval($start), "line" => intval($orientedLine), "distance" => $distance];
							array_push($currentRoute,$currentSegment);
							recursive($hub, $end, $currentRoute);
							array_pop($currentRoute);
						}
					}
				}
			}
		}
	//}
}

function getStopName($stop) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT * FROM `transport_stops` WHERE id = :stop");
	$req->execute(array(
		":stop" => $stop,
	));
	$data = $req->fetch();
	return $data['name'];
}

function getLineName($line) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT L.name AS line, LO.name AS line_oriented FROM `transport_lines_oriented` LO
		JOIN `transport_lines` L ON L.id = LO.line_id
		WHERE LO.id = :line");
	$req->execute(array(
		":line" => $line,
	));
	$data = $req->fetch();
	return $data;
}

function getLinesFromStop($stop) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT L.code AS code
		FROM `transport_lines_oriented_stops_order`
		JOIN transport_lines_oriented L ON L.id = line_oriented_id
		WHERE stop_id = :stop
		GROUP BY L.code");
	$req->execute(array(
		":stop" => $stop,
	));
	$data = $req->fetchAll();
	$lines = [];
	foreach ($data as $line) {
		array_push($lines, $line['code']);
	}
	return $lines;
}

function getLineOriented($start,$end,$line) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT DISTINCT A.line_oriented_id FROM `transport_lines_oriented_stops_order` A
		INNER JOIN `transport_lines_oriented_stops_order` B ON A.line_oriented_id = B.line_oriented_id
		JOIN `transport_lines_oriented` L ON L.id = A.line_oriented_id
		WHERE A.stop_id = :start
		AND B.stop_id = :end
		AND A.rank < B.rank
		AND L.code = :line
		LIMIT 1");
	$req->execute(array(
		":start" => $start,
		":end" => $end,
		":line" => $line,
	));
	$data = $req->fetch();
	return $data['line_oriented_id'];
}

function getHubsFromLine($line,$stops) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT *
			FROM (SELECT stop_id, COUNT(stop_id)/2 AS cnt
			FROM `transport_lines_oriented_stops_order` LS
			JOIN `transport_lines_oriented` L ON L.id = LS.line_oriented_id
			WHERE stop_id IN
				(SELECT stop_id
				FROM `transport_lines_oriented_stops_order` LS
				JOIN `transport_lines_oriented` L ON L.id = LS.line_oriented_id
				WHERE stop_id NOT IN (".$stops.") AND L.code = :line)
			GROUP BY stop_id) X
		WHERE cnt > 1");
	$req->execute(array(
		":line" => $line
	));
	$data = $req->fetchAll();
	return $data;
}

function compareLines($lines1,$lines2) {
	$commonLines = [];
	foreach ($lines1 as $line1) {
		for ($i = 0; $i < sizeof($lines2); $i++) {
			if ($line1 == $lines2[$i]) {
				array_push($commonLines, $line1);
			}
		}
	}
	return $commonLines;
}

function getDistance($start,$end,$line) {
	$distance = 0;
	$stops = getStopsBetween($start,$end,$line);
	foreach ($stops as $stop) {
		$latTo = $stop['lat'];
		$lonTo = $stop['lon'];
		if (isset($latFrom) && isset($lonFrom)) {
			$distance += haversineGreatCircleDistance($latFrom,$lonFrom,$latTo,$lonTo);
		}
		$latFrom = $stop['lat'];
		$lonFrom = $stop['lon'];
	}
	return $distance/1000;
}

function getStopsBetween($start,$end,$line) {
	$dbh = $GLOBALS['dbh'];
	$req = $dbh->prepare("SELECT S.id, rank, X(position) AS lat, Y(position) AS lon FROM `transport_lines_oriented_stops_order` LS
		JOIN `transport_stops` S ON S.id = LS.stop_id
		WHERE LS.rank >= (SELECT rank FROM `transport_lines_oriented_stops_order` LS
		JOIN `transport_stops` S ON S.id = LS.stop_id
		WHERE LS.line_oriented_id = :line AND LS.stop_id IN (:start,:end) ORDER BY LS.rank ASC LIMIT 1)
		AND LS.rank <= (SELECT rank FROM `transport_lines_oriented_stops_order` LS
		JOIN `transport_stops` S ON S.id = LS.stop_id
		WHERE LS.line_oriented_id = :line AND LS.stop_id IN (:start,:end)	ORDER BY LS.rank DESC LIMIT 1) AND line_oriented_id = :line");
	$req->execute(array(
		":start" => $start,
		":end" => $end,
		":line" => $line,
	));
	$data = $req->fetchAll();
	return $data;
}

function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;

	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	return $angle * $earthRadius;
}
