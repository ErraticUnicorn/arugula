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
function createGame($name) {
	global $mysqli;
	$query = "INSERT INTO `games` (name) VALUES($name)" ;
	if ($result = $mysqli->query($query)) {
		echo "You inserted $name.";
		$result->free();
	}
} 
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
	// Run through the array of all players you received and compare lat + long for collisions
	checkEnemyCollisions($teamid, $lat, $long);
	// Then, call a query using that passed player's player id that fetches the associated game id, and bind that game id to a variable
	$gameid = getGameID($playerid);
	// Then call a query using that game id to get all flags bound to that game (in the object_junction).
	//getFlagID returns an array containing both flag objects in the game. We care about collisions with the enemy flag.
	$flags = getFlagID($gameid);
	//pull team info from $flags
	/*
	if ($flags[2] == $teamid) {
		$flagID = $flag[0];
	} else if ($flags[7] == $teamid) {
		$flagID = $flag[5];
	}
	echo $flagID;
	*/
	//That means we want to get the flag that belongs to the enemy team, and ignore the other one
	// $query = "SELECT `team` from `objects` WHERE `object_id` = $ "
	
	// Run through the array of all flags you received and compare lat + long for collisions
	// checkFlagCollision not implemented
}

//HELPER FUNCTIONS

function updateLocation($playerid, $lat, $long){
	global $mysqli;
	$query = "UPDATE `players` SET `lat` = $lat, `long` = $long WHERE `player_id` = $playerid " ;
	if($result = $mysqli->query($query)){
		echo "You updated '$playerid' ." ;
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
	$query = "SELECT  `game_id` FROM  `player_junction` WHERE  `player_id` =$playerid";
	if ($result = $mysqli->query($query)) { 
	//This is ok because we should only get ONE ROW for our result
		while ($row = $result->fetch_row() ) {
			return $row[0];
		}
	}
}

function getFlagID($gameid){
	global $mysqli;
	$query = "SELECT `object_id` FROM `object_junction` WHERE `game_id` = $gameid";
	if ($result = $mysqli->query($query)) { 
	//This is ok because we should only get ONE ROW for our result
		while ($row = $result->fetch_assoc() ) {
			return $row;
		}
	}
}

// Implement collision handler 4 this
function checkEnemyCollisions($teamid, $lat, $long){
	global $mysqli;
	$query = "SELECT `player_id`, `lat`, `long` FROM  `players` WHERE  `team` != $teamid";
	
	if ($result = $mysqli->query($query)) {
		/* fetch associative array */
		while ($row = $result->fetch_row() ) {
			if($row[1] == $lat && $row[2] == $long) {
				echo "Collision with $row[0] \n"; //COLLISION HANDLER HERE
			} else {
				// NO COLLISION
			}
			
		}
		$result->free();
	}
}

//David assures me this works (NO COLLISION HANDLER YET)
function checkFlagCollision($flagID, $lat, $long){
	global $mysqli;
	$query = "SELECT `object_id`, `lat`, `long` FROM `objects` WHERE `object_id` = $flagID";
	if($result = $mysqli->query($query)){
		while($row = $result->fetch_row()){
			if($row[1] == $lat && $row[2] == $long){
				echo "Collision!"; //COLLISION HANDLER HERE
			} else {
				echo "No Collision";//NO COLLISION
			}
		
		}
	
	}
}

// aka Flag-Player collision
function flagPickup ($objectid, $playerid) {
global $mysqli;
$query = "UPDATE `objects` SET `type` = $playerid where `object_id` = $objectid";
	if($result = $mysqli->query($query)) {
		echo "Updated flag to be bound to $playerid";
	}
}

// aka player-player collision, when one player is holding the flag
function flagDrop ($objectid, $playerid) {
global $mysqli;
$query = "UPDATE `objects` SET `type` = -1 WHERE `object_id` = $objectid";
	if ($result = $mysqli->query($query)) {
		echo "Updated flag to be free ";
	}
}

// resolving being 'jailed' after collisions
function boundTag() {
global $mysqli;
//$query = "
//update player table 2 have a column 4 state
}

// aka player-bound collision
function flagCapture($objectid, $playerid) {

function enemyCollisionHandler(){
//Check to see if enemy is carrying the flag. If they are carrying the flag, the flag is dropped (flag and player's state get changed)
//Otherwise no effect
}

Function flagCollisionHandler(){
//Flag collision needs to update the flag's state to be 'picked up' and the player who picked it up to be 'carrying'
}

/*Explanation of States for Flags and Players
Flags:
0 - Default - the flag is 'on the ground' and free to be picked up
1 - Carried - The flag has been picked up and cannot be interacted with directly

Players:
0 - Default - Player has normal behavior when colliding.
1 - Carrying - The player is carrying the flag. If collided with, the flag will be dropped
2 - Jailed - The player must return to their bounds before they can collide with others.


*/

global $mysqli;
$query = "UPDATE `objects` SET `type` = -1 WHERE `object_id` = $objectid";
	if ($result = $mysqli->query($query)) {
		echo "$playerid captured the enemy team's flag!";
	}
}

Flight::route('/@type(/@param1(/@param2(/@param3)))', function($type, $param1, $param2, $param3){
    if ($type == "getgame" && $param1 != null) {
        getGame($param1); 
    } else if ($type == "creategame" && $param1 !=null) {
		createGame($param1);
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
	} 	
});

Flight::start();
//Close DB connection
$mysqli->close();
?>
