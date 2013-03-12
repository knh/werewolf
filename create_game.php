<?php
require_once('init.php');

$raw_role_string = $_POST['role_list'];
$role_array = preg_split("/(\r\n|\n|\r)/", $raw_role_string); // parse the raw string
$role_num_array = array(); //Holds the final result of parsed role_array.
$counter = 0; 
$player_num = 0;

//Parse the role_array. Final result is an array[$role] = num
foreach($role_array as &$role){
	$sub_role = explode("*", $role);
	if(isset($sub_role[1])){
		$role=$sub_role[0];
		$role_num_array[$counter] = (int)$sub_role[1];
	}
	else{
		$role_num_array[$counter] = 1;
	}
	$player_num += $role_num_array[$counter];
	$counter++;
}
// Output the role table.
for($counter=0; $counter < count($role_array); $counter++){
	echo $role_array[$counter] . " * " . $role_num_array[$counter] ."</br>";
}
$game_id = mt_rand(100000, 999999); //create a better random game id.
$query="SELECT * FROM werewolf_gameid WHERE game_id=" . $game_id;

// to make sure the game id is unique
while(true){
	$result=$mysqli->query($query);
	if($result->num_rows == 0)
		break;
	$game_id=rand(100000, 999999);
	$query="SELECT * FROM werewolf_gameid WHERE game_id = " . $game_id;
}
if($mysqli->query("INSERT INTO werewolf_gameid (game_id, player_num_limit)" .
	"VALUES ('" . $game_id . "', '" . $player_num ."')") == true){
	echo "</br>Create game succeed. Your game id is " . $game_id .". Your friend can use this number to join in the game.</br>";
}else{
	echo "</br>Create game failed. Please try again later.</br>";
}

$query="INSERT INTO werewolf_detail (game_id, all_roles) VALUES ('" . $game_id . "', '" . $raw_role_string . "')";

if($mysqli->query($query)){
	echo "Redirecting to game console in 3 seconds. Do not refresh the page. </br>";
	echo "<script>window.setInterval(function(){document.location='./control_game.php?game_id=". $game_id . "'}, 3000)</script>"; // redirecting to control_game.php after creating game.
}else{
	echo "Detail writing failed. </br>";
}
?>