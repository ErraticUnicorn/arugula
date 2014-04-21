<?php
//require statements
require 'flight/Flight.php';

//DB connection initialization
	$host = "stardock.cs.virginia.edu"; 
	$user = "cs4720djw2yw"; 
	$pass = "spring2014"; 
	$table = "cs4720djw2yw";
	$mysqli = new mysqli($host, $user, $pass, $table);
	if ( mysqli_connect_errno() ) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	} else {
		//echo "MySQL connection established <br>"; 
	}	
	
//functions 

//getAllGames function
//returns all columns for all games in the games table
function getAllGames() {
	//echo "Debug: getAllGames() called <br>";
	// Adapted from PHP Manual example
	global $mysqli;
	/* query */
	$query = "SELECT * FROM `games`";
	echo "[";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		$numRow = $result->num_rows;
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			echo json_encode($row);
			if( $i < ($numRow - 1) ){
				echo ",";
				$i++;
			}
		}
		/* free result set */
		$result->free();
	}
	echo "]";
}

// getGame function 
// given a game ID, 
// return all columns for all games from the table that match that id (id is unique, always returns <= one row) 
function getGame($gameID) {
	global $mysqli;
	/* query */
	$query = "SELECT * FROM `games` WHERE `game_id` = $gameID";
	echo "[";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		while ($row = $result->fetch_assoc()) {
			echo json_encode($row);
		}
			/* free result set */
		$result->free();
	}
	echo "]";
}

// createGame function
// inserts/creates a game in the game table with the name value initialized to the name parameter passed
/*
function createGame($name) {
	global $mysqli;
	$query = "INSERT INTO games (`name`) VALUES('$name')" ;
	if ($result = $mysqli->query($query)) {
		echo "You inserted $name.";
		$result->free();
	}
} */

//getPlayers function
//returns all players who are members of the given team as a json array
function getPlayers($teamid) {
	global $mysqli;
	// mysql query to update the player (identified by $playerid)'s row to have contain the passed $lat, $long coordinates
	$query = "SELECT * FROM `players` where `team` = $teamid " ;
	
	echo "[";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		while ($row = $result->fetch_assoc()) {
			echo json_encode($row);
		}
			/* free result set */
		$result->free();
	}
	echo "]";
}

//makeFlag function
//creates a flag with team = $team, lat = $lat and long = $long
function makeFlag ($team, $lat, $long) {
	global $mysqli;
	$query = "INSERT INTO `objects` (`team`, `lat`, `long`) VALUES($team, $lat, $long)" ;
	
	if ($result = $mysqli -> query($query)){ 
		echo "You created a flag with properties team = $team, lat = $lat, long = $long." ;
		$result->free();
	}
}

//getFlag function
//retrieves info on a flag's location (returns lat long)
function getFlag ($flagid) {
	global $mysqli;
	$query = "SELECT * FROM `objects` WHERE `object_id` = $flagid" ;
	
	echo "[";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		while ($row = $result->fetch_assoc()) {
			echo json_encode($row);
		}
			/* free result set */
		$result->free();
	}
	echo "]";
}

// gpsUpdate function
// updates player info in the players table to have the passed lat, long coordinates
// then compares those lat, long coordinates to other players/flags to check for collisions
function gpsUpdate($playerid, $lat, $long) {
	global $mysqli;
	//Everything has been shunted to helper methods
	// mysql query to update the player (identified by $playerid)'s row to have contain the passed $lat, $long coordinates
	updateLocation($playerid, $lat, $long);
	// call a query using the passed player's player id that fetches their team id, and bind that team id to a variable
	$teamid = getTeam($playerid);
	// Then, call a query using that team id to get all players on the enemy team

	// Then, call a query using the passed player's player id that fetches the associated game id, and bind that game id to a variable
	$gameid = getGameID($playerid);
	// Then call a query using that game id to get the enemy flag bound to that game (flags = one to many relation, handled in objects table).
	$alliedFlag = getAlliedFlagID($gameid, $teamid);
	
	$enemyFlag = getEnemyFlagID($gameid, $teamid);
	
	// Check enemy flag's lat/long for collision 
	$flagCol = checkFlagCollision($enemyFlag, $lat, $long);
	if($flagCol == true) {
		pickupFlag($enemyFlag, $playerid, $teamid, $gameid);
	}
	// Check for collisions with enemy players
	$playerCol = checkEnemyCollisions($teamid, $lat, $long);
	if ($playerCol != -1) {
		tag($playerid, $playerCol, $alliedFlag, $teamid, $gameid);
	}
}

//HELPER FUNCTIONS FOR GPSUPDATE

function updateLocation($playerid, $lat, $long){
	global $mysqli;
	$query = "UPDATE `players` SET `lat` = $lat, `long` = $long WHERE `player_id` = $playerid " ;
	if($result = $mysqli->query($query)) {
		//echo "You updated '$playerid' ." ;
	}
}

function getTeam($playerid){
	global $mysqli;
	$query = "SELECT `team` FROM `players` WHERE `player_id` = $playerid " ;
	if ($result = $mysqli->query($query)) { 
	//This is ok because we should only get ONE ROW for our result
		while ($row = $result->fetch_row() ) {
			return $row[0];
		}
	}
}

function getGameID($playerid){
	global $mysqli;
	$query = "SELECT  `game_id` FROM  `player_junction` WHERE  `player_id` = $playerid";
	if ($result = $mysqli->query($query)) { 
	//This is ok because we should only get ONE ROW for our result
		while ($row = $result->fetch_row() ) {
			return $row[0];
		}
	}
}

function getAlliedFlagID($gameid, $teamid){
	global $mysqli;
	$query = "SELECT `object_id` FROM `objects` WHERE `game_id` = $gameid AND `team` = $teamid";
	if ($result = $mysqli->query($query)) {
		//returns an array, not a row
		while ($row = $result->fetch_row() ) {
			return $row[0];
		}
	}
}

function getEnemyFlagID($gameid, $teamid){
	global $mysqli;
	$query = "SELECT `object_id` FROM `objects` WHERE `game_id` = $gameid AND `team` != $teamid";
	if ($result = $mysqli->query($query)) { 
	//returns an array, not a row
		while ($row = $result->fetch_row() ) {
			return $row[0];
		}
	}
}


// checkEnemyCollisions checks to see if you collide with an enemy, and returns 
// the player_id of the first enemy you collide with, or -1 if you collide with no enemies
function checkEnemyCollisions($teamid, $lat, $long){
	global $mysqli;
	$query = "SELECT `player_id`, `lat`, `long` FROM  `players` WHERE  `team` != $teamid";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		while ($row = $result->fetch_row() ) {
			if($row[1] == $lat && $row[2] == $long) {
				//echo "DEBUG: PLAYER COLLISION WITH PLAYER $row[0] \n";
				return $row[0]; // return playerid of whomever you collided with
			} else {
				//echo "DEBUG: NO PLAYER COLLISION \n";
				return -1;// NO COLLISION
			}
		}
	}
}

// checkFlagCollisions checks to see if you collide with a flag, and returns
// true if you do
function checkFlagCollision($flagID, $lat, $long){
	global $mysqli;
	$query = "SELECT `object_id`, `lat`, `long` FROM `objects` WHERE `object_id` = $flagID";
	if($result = $mysqli->query($query) ) {
		while($row = $result->fetch_row() ) {
			if($row[1] == $lat && $row[2] == $long){
				//echo "DEBUG: FLAG COLLISION WITH FLAG $row[0] \n";
				return true; //true = COLLISION, CALL COLLISION HANDLER
			} else {
				//echo "DEBUG: NO FLAG COLLISION \n";
				return false; // false = no collision, do nothing
			}
		
		}
	
	}
}

// pickupFlag function
// aka player-flag collision handler
// check the Flag's state. If 0 (free), change to 1 (carried) and change  player state to indicate they are carrying flag (1)


function pickupFlag ($objectid, $playerid, $teamid, $gameid) {
//echo "DEBUG: pickupFlag called! ";
global $mysqli;
$query = "SELECT `state` FROM `objects` WHERE `object_id` = $objectid ";
	if($result = $mysqli->query($query)) {
		while($row = $result->fetch_row() ) {
			if ($row[0] == 0) {
				$query2 = "UPDATE `objects` SET `state` = 1 WHERE `object_id` = $objectid ; UPDATE `players` SET `state` = 1 WHERE `player_id` = $playerid";
				if( $result2 = $mysqli->multi_query($query2) ) {
					//echo "Flag picked up!"; // Call a query to create an event
					//echo "DEBUG: Event Handler Called: Parameters: $gameid $playerid $teamid \n";
					eventHandler("pickup", $gameid, $playerid, $teamid, -1);
				}
			}
		}
	}
}


// tag function
// aka player-player collision handler
// check the taggee's state - if 1 (carrying flag), change it to 2, and change the flag state to 0 (free)
// check the player's state. If 1 (carrying flag), update the player's state to 2 (tagged), and update the flag's state to 0.
function tag ($tagger, $taggee, $flag, $teamid, $gameid) {
	global $mysqli;
	$query = "SELECT `state` FROM `players` WHERE `player_id` = $taggee ";
	if($result = $mysqli->query($query)) {
		while($row = $result->fetch_row() ) {
			if ($row[0] == 1) {
				//echo "DEBUG: flag = $flag, tagger = $tagger, taggee = $taggee \n";
				$query2 = "UPDATE `objects` SET `state` = 0 WHERE `object_id` = $flag ; UPDATE `players` SET `state` = 2 WHERE `player_id` = $taggee";
				if( $result2 = $mysqli->multi_query($query2) ) {
					//echo "Player $taggee tagged by $tagger!, flag $flag dropped!"; // Call a query to create an event
					eventHandler("tag", $gameid, $tagger, $teamid, $taggee);
				}
			}
		}
	}
}

// boundColResolver function
// resolves both player-bound collisions (unjailing and flag capturing)
// check the player's state. If 2 (tagged), update the player's state to 0.
// else if player's state = 1, update the player's state to 0 and score a point (if that team's flag is uncarried?)

//THIS FUNCTION NOT TESTED DUE TO BOUNDS BEING UNIMPLEMENTED
//ALSO NOT FINISHED
//assumption - bounds is an array of points
function boundColResolver ($tagger, $flag, $teamid, $gameid, $bounds) {
	global $mysqli;
	$query = "SELECT `state` FROM `players` WHERE `player_id` = $tagger ";
	if($result = $mysqli->query($query)) {
		while($row = $result->fetch_row() ) {
			if ($row[0] == 2) {
				$query2 = "UPDATE `players` SET `state` = 2 WHERE `player_id` = $tagger";
				if( $result2 = $mysqli->query($query2) ) {
					echo "Player $tagger freed from jail"; // Call a query to create an event
					eventHandler("freed", $gameid, $tagger, $teamid, -1);
				}
			} else if ($row[0] == 1) {
				$query2 = "UPDATE `objects` SET `state` = 0 WHERE `object_id` = $flag ; UPDATE `players` SET `state` = 0 WHERE `player_id` = $tagger";
				if( $result2 = $mysqli->multi_query($query2) ) {
					echo "Player $tagger captured the flag"; 
					
					if($teamid == 1){
						$query3 = "SELECT `score1` from `games` WHERE `game_id = $gameid";
						if($result3 = $mysqli->query($query3)){
							while($r = $result3->fetch_row() ){
								$newScore = $r[0] + 1;
								$query4 = "UPDATE `games` SET `score1` = $newScore  WHERE `game_id` = $gameid"; //team one scores
								$mysqli->query($query4);
								checkWinCond ($gameid, $teamid, $tagger);
								eventHandler("score1", $gameid, $tagger, $teamid, -1);
							}
						}	
					} else if ($teamid == 2) {
						$query3 = "SELECT `score2` from `games` WHERE `game_id = $gameid";
						if($result3 = $mysqli->query($query3)){
							while($r = $result3->fetch_row() ){
								$newScore = $r[0] + 1;
								$query4 = "UPDATE `games` SET `score2` = $newScore  WHERE `game_id` = $gameid"; //team two scores
								$mysqli->query($query4);
								checkWinCond ($gameid, $teamid, $tagger);
								eventHandler("score2", $gameid, $tagger, $teamid, -1);
							}
						}	
					}
				}
			}
		}
	} 	
}

//Whenever score is updated, check win condition.
//NOT FULLY TESTED DUE TO EVENTS BEING UNIMPLEMENTED
function checkWinCond ($gameid, $teamid, $playerid){
	global $mysqli;
	$query = "SELECT `score1`, `score2` from `games` where `game_id` = $gameid";
	if($result = $mysqli->query($query)) {
		while($row = $result->fetch_row() ) {
			if($row[0] >= 3) {
				echo "Team 1 has won!"; //call an event
				eventHandler("win1", $gameid, $playerid, $teamid, -1);
			} else if ($row[1] >= 3) {
				echo "Team 2 has won!"; //call an event
				eventHandler("win2", $gameid, $playerid, $teamid, -1);
			}
		}
	}
}

// we need to handle events!
// Event key
// 0 = 
// 1 = team one victory
// 2 = team two victory
// 3 = team one scored
// 4 = team two scored
// 5 = player freed from jail
// 6 = played tagged another player
// 7 = flag picked up
function eventHandler($event, $param1, $param2, $param3, $param4) {
	global $mysqli;
	while ($mysqli->more_results()) {
		$mysqli->next_result();
	}
	if ($event == "win1") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES(1, $param1, $param2, $param3)" ;
		
		if ( $result = $mysqli->query($query) ) {
			echo "1";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "win2") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES(2, $param1, $param2, $param3)" ;
		
		if ( $result = $mysqli->query($query) ) {
			echo "2";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "score1") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES(3, $param1, $param2, $param3)" ;
		;
		if ( $result = $mysqli->query($query) ) {
			echo "3";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "score2") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES(4, $param1, $param2, $param3)" ;
		if ( $result = $mysqli->query($query) ) {
			echo "4";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "freed") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES(5, $param1, $param2, $param3)" ;
		if ( $result = $mysqli->query($query) ) {
			echo "5";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "tag") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid, $param4 = target_player
		$query = "INSERT INTO `events` (type, game, player, team, target_player) VALUES(6, $param1, $param2, $param3, $param4)" ; // how to pass 2 player names?
		if ( $result = $mysqli->query($query) ) {
				echo "6";
		} else {
			echo "$mysqli->error";
		}
	} else if ($event == "pickup") { // $param1 = $gameid, $param2 = $playerid, $param3 = $teamid
		$query = "INSERT INTO `events` (type, game, player, team) VALUES('7', $param1, $param2, $param3)" ;
		if ( $result = $mysqli->query($query) ) {
			echo "7";
		} else {
			echo "$mysqli->error";
		}
		
		
	}
}

//and an event getter!
function getEventFeed($gameid) {
	global $mysqli;
	$query = "SELECT * FROM `events` where `game` = $gameid ORDER BY `event_id`";
	echo "[";
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		$numRow = $result->num_rows;
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			echo json_encode($row);
			if( $i < ($numRow - 1) ){
				echo ",";
				$i++;
			}
		}
		/* free result set */
		$result->free();
	}
	echo "]";
}

/*Explanation of States for Flags and Players
Flags:
0 - Default - the flag is 'on the ground' and free to be picked up
1 - Carried - The flag has been picked up and cannot be interacted with directly

Players:
0 - Default - Player has normal behaviour when colliding.
1 - Carrying - The player is carrying the flag. If collided with, the flag will be dropped
2 - Jailed - The player must return to their bounds before they can collide with others.
*/

//pointInPolygon($lat, $long, $latBounds, $longBounds, $n) 
// given a point ($lat,$long) and an array of points ($latBounds, $longBounds) of size $n
// check if that point is inside the polygon created by that array or not
// adapted from http://alienryderflex.com/polygon/

 /*
function pointInPolygon($lat,$long, $latBounds, $longBounds, $n) {
	$j = $n -1;
	$oddNodes = false;
	//latitude = Y coordinate, longitude = X coordinate
	
	for ($i=0; $i<$n; $i++) {
		if ($latBounds[$i]<$lat && $latBounds[$j]>=$lat ||  $latBounds[$j]<$lat && $latBounds[$i]>=$lat) {
			if ($longBounds[$i]+($lat-$latBounds[$i])/($latBounds[$j]-$latBounds[$i])*($longBounds[$j]-$longBounds[$i])<$long) {
				$oddNodes =! $oddNodes; 
			}
		} 
		$j=$i;
	 }
var_dump(oddNodes);
return oddNodes; 
}*/

function pointInPolygon($lat, $long, $latBounds, $longBounds, $n) {
	//echo "PIP called \n";
	$i = $n;
	$j = $n;
	$c = false;
	
	  for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
		if( (( ($latBounds[$i] >= $lat) != ($latBounds[$j] >= $lat) )) &&  ( ($long <= ($longBounds[$j] - $longBounds[$i]) * ($lat - $latBounds[$i]) / ($latBounds[$j] - $latBounds[$i]) + $longBounds[$i]) ) ) {
			//if ( ( ($latBounds[$i]) >= point.y ) != (points[j].y >= point.y) ) && (point.x <= (points[j].x - points[i].x) * (point.y - points[i].y) / (points[j].y - points[i].y) + points[i].x) )
			//for (i = 0, j = nvert-1; i < nvert; j = i++) { 
			//if ( ((verty[i]>testy) != (verty[j]>testy)) &&(testx < (vertx[j]-vertx[i]) * (testy-verty[i]) / (verty[j]-verty[i]) + vertx[i]) )
			$c = !$c;
		}
		
	}
	return $c;
	
}



function pointinPolygonCaller ($x, $y, $gameid, $teamid) {
	global $mysqli;
	//echo "PIPCaller called \n";
	$query = "SELECT `latBounds`, `longBounds` FROM `bounds` WHERE `game_id` = $gameid AND `team_id` != $teamid";
	$latBounds = array();
	$longBounds = array();
	if($result = $mysqli->query($query)) {
		//echo "Query successful \n";
		$n = $result->num_rows;
		//echo "$n";
		$flip = true;
		for($i = 0; $i < $n-1; $i++) {
			while ($row = $result->fetch_row()) {
			//for($i = 0; $i < $n-1; $i++) {
				if(array_key_exists(0, $row)){
					array_push ($latBounds, $row[0]);
				} if(array_key_exists(1, $row)){
					array_push ($longBounds, $row[1]);
				}
			}
		}
		//var_dump($latBounds);
		//var_dump($longBounds);
		$answer = pointInPolygon($y, $x, $latBounds, $longBounds, $n);
		//var_dump($answer);
		if($answer == true){
			echo "The point lies within the bounds.";
		} else if ($answer != true) {
			echo "The point is outside the bounds.";
		}
	} else {
		echo "$mysqli->error";
	}

}

//betterCreateGame()
//NEEDS to implement a way to generate random lat/long for flags, given bounds. 

function createGame($name, $bounds1, $bounds2) { //take in bounds as /creategame/x,y;x,y;x,y; -  bounds param a string you parse and split into coordinate array - requires two, one for each team
	global $mysqli;
	$bound1Array = explode(';', $bounds1);
	$bound2Array = explode(';', $bounds2);
	
	//2d arrays for storing all the coordinates to pass to Jack's code
	$rb1a = Array(Array());
	$rb2a = Array(Array());
	
	$query = "INSERT INTO `games` (name) VALUES($name)" ;
	if ($result = $mysqli->query($query)) {
		//echo "You inserted $name.";
		$result->free();
	}
	$gameid = $mysqli->insert_id;
			//MULTIQUERY TO ADD ALL PASSED BOUNDS
	$query2 = "";
			//$query2 = "INSERT INTO `objects` (game_id, state, team, lat, long) VALUES($row[0], 0, 1, 1, 1 )" ; //instantiate team one's flag
	foreach($boun1dArray as $a) {
		$a2 = explode(',', $a);
		array_push($rb1a, $a2[0]);
		array_push($rb1a, $a2[1]);
		$query2 . "INSERT INTO bounds (`game_id`, `team_id`, `latBounds`, `longBounds`) VALUES ('$game_id', '1', '$a2[0]', '$a2[1]');";
	}
	foreach ($bound2Array as $a3) {
		$a4 = explode(',', $a3);
		array_push($rb2a, $a4[0]);
		array_push($rb2a, $a4[1]);
		$query2 . "INSERT INTO bounds (`game_id`, `team_id`, `latBounds`, `longBounds`) VALUES ('$game_id', '2', '$a4[0]', '$a4[1]');";
	}
	//CALL JACK'S CODE
	$points1 = pickPointsInBounds($rb1a, 1); //One point, for now.
	$points2 = pickPointsInBounds($rb2a, 1); // ''
	
	$query2 . "INSERT INTO objects (`game_id`, `state`, `team`, `lat`, `long`) VALUES('$gameid', '0', '1', '$points1[0]', '$points1[1]') ;" ; //instantiate team one's flag
	$query2 . "INSERT INTO objects (`game_id`, `state`, `team`, `lat`, `long`) VALUES('$gameid', '0', '2', '$points2[0]', '$points2[1]') ;" ; //instantiate team two's flag
	
	if ($result = $mysqli->multi_query($query2)) {
		//echo "You inserted bounds!
		$result->free();
	} else {
		echo "$mysqli->error";
	}
}


function addPlayer($team) {
	global $mysqli;
	$query = "INSERT INTO `players` (team) VALUES ($team)";
	if ($result = $mysqli->query($query)) {
		$result->free();
	}
}
//addPlayer()

function postBounds($gameid, $teamid, $latBound, $longBound) {
	global $mysqli;
	$query = "INSERT INTO bounds (`game_id`, `team_id`, `latBounds`, `longBounds`) VALUES ('$gameid', '$teamid', '$latBound', '$longBound')";
	if ($result = $mysqli->query($query) ) {
		echo "Bounds posted";
		$result->free();
	}  else {
		echo "$mysqli->error";
	}

}

function joinGame($gameid, $teamid){
	global $mysqli;
	$query = "INSERT INTO players (`game_id`, `team`) VALUES ('$gameid', '$teamid')";
	if ($result = $mysqli->query($query)) {
		echo "$mysqli->insert_id";
	} $result->free();
}

//Beginning of Jack's code
/*
function pickPointsInBounds($bounds, $numPoints) {
	$points = Array();
	$triangles = triangulate($bounds);

	for($i = 0; $i < $numPoints; $i++) {

		$rand = mt_rand() / mt_getrandmax();

		$area = 0;
		foreach($triangles as $t) {
			$area += $t[3];
		}

		$r = $rand * $area;

		$current = 0;
		foreach($triangles as $t) {
			$current += $t[3];
				
			if($current > $r) {
				$p = randInTriangle($t);

				if(isValid($p)) {
					array_push($points, $p);
						
					if(array_count_values($points) == $numPoints) {
						return $points;
					}
				}

				break;
			}
		}
	}

	return $points;
} 

function isValid($p) {
	$ground = 0xffffff;
	$groundCity = 0x700000;

	$i = imagecreatefrompng(getURL($p));

	$c = dechex(imagecolorat($i, 4, 4));

	return $c == $ground || $c == $groundCity;
}

function triangulate($points) {

	if(array_count_values($points) < 3) {
		return [];
	}

	$t = array_slice($points, 0, 3);

	$area = abs((($t[0][0]*($t[1][1] - $t[2][1]))+($t[1][0] * ($t[0][1]-$t[2][1]))+($t[2][0] * ($t[0][1] - $t[1][1])))/2);
	array_push($t, $area);

	if(array_count_values($points) == 0) {
		return [$t];
	}

	array_unshift($points, $t[0], $t[2]);

	return array_push(triangulate($points), $t);
}

function randInTriangle($t) {

	$rand1 = mt_rand() / mt_getrandmax();
	$rand2 = mt_rand() / mt_getrandmax();

	$tmp = sqrt($rand1);

	$lat = (1-$tmp) * $t[0][0] + ($tmp * (1-$rand2)) * $t[1][0] + ($tmp * $rand2) * $t[2][0];
	$lng = (1-$tmp) * $t[0][1] + ($tmp * (1-$rand2)) * $t[1][1] + ($tmp * $rand2) * $t[2][1];

	return [$lat, $lng];
} 

function getURL($p) {
	return "http://maps.googleapis.com/maps/api/staticmap"
			. "?center=$p[0],$p[1]"
			. "&zoom=21"
	. "&format=png"
	. "&sensor=false"
	. "&size=10x10"
	. "&maptype=roadmap"
	. "&style=feature:administrative|element:geometry|color:0x7d8080|visibility:off"
	. "&style=element:labels|color:0x808080|visibility:off"
	. "&style=feature:poi|element:geometry|visibility:off"
	. "&style=feature:road|color:0x000000"
	. "&style=feature:transit|element:geometry|color:0x000000"
	. "&style=feature:water|element:geometry|color:0x000000"
	. "&style=feature:landscape|element:geometry.stroke|visibility:off"
	. "&style=feature:landscape.natural|color:0xffffff"
	. "&style=feature:landscape.man_made|lightness:-74|saturation:100|gamma:0.75|hue:0xff0000";
} 
*/
//End of Jack's code



Flight::route('/@type(/@param1(/@param2(/@param3(/@param4))))', function($type, $param1, $param2, $param3, $param4){
	if ($type == "getgame" && $param1 != null) {
		getGame($param1); // /arugula/getgame/(gameid)
	} else if ($type == "creategame" && $param1 !=null && $param2 != null && $param3 != null) {
		createGame($param1, $param2, $param3); // /arugula/creategame/(name)/(boundstr1)/(boundstr2)
	} else if ($type == "getall") {
		getAllGames();
	} else if ($type == "gpsupdate" && $param1 != null && $param2 != null && $param3 != null){
		gpsUpdate($param1, $param2, $param3);
	} else if ($type == "getplayers" && $param1 != null) {
			getPlayers($param1);
	} else if ($type == "makeflag" && $param1 != null && $param2 != null && $param3 != null){
		makeFlag($param1, $param2, $param3);
	} else if ($type == "getflag" && $param1 != null) {
			getFlag($param1);
	} else if ($type == "eventfeed" && $param1 != null) {
			getEventFeed($param1);
	} else if ($type == "postbound" && $param1 != null && $param2 != null && $param3 != null && $param4 != null){
		postBounds($param1, $param2, $param3, $param4);
	} else if ($type == "getgameid" && $param1 != null) {
			getGameID($param1);
	} else if ($type == "joingame" && $param1 != null && $param2 != null) {
		joinGame($param1, $param2);
	} else if ($type == "pip" && $param1 != null && $param2 != null && $param3 != null && $param4 != null) {
		pointinPolygonCaller($param1, $param2, $param3, $param4);
	}
});

Flight::start();
//Close DB connection
$mysqli->close();
?>