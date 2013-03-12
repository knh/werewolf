<?php
require_once('init.php');

$game_id = (int) $_GET['game_id']; // prevent SQL injection attacks, duh!

echo "Your game id is " . $game_id; // prevent XSS injection attacks

//Query the database
$query="SELECT * FROM werewolf_detail WHERE game_id = " . $game_id;
$result=$mysqli->query($query) or die ("Query failed.");

//parse the all_role string. The final result stores in all_role_table. 
//TODO: Refactor it after a better understanding of php.
$temp_row = mysqli_fetch_array($result);
$raw_role_string = $temp_row['all_roles'];
$role_array = preg_split("/(\r\n|\n|\r)/", $raw_role_string);
$role_num_array = array();
$counter = 0;
$player_num = 0;
$all_role_table = array();
foreach($role_array as &$role){
	$sub_role = explode("*", $role);
	if(isset($sub_role[1])){
		$role=$sub_role[0];
		$all_role_table[$role]=(int)$sub_role[1];
		$role_num_array[$counter] = (int)$sub_role[1];
	}
	else{
		$all_role_table[$role]=(int) 1;
		$role_num_array[$counter] = 1;
	}
	$player_num += $role_num_array[$counter];
	$counter++;
}

//parse the current_roles string. result stores in curr_role_table.
$curr_role_table=array();
foreach($all_role_table as $key => $val){
	$curr_role_table[$key] = 0;
}
$raw_curr_role_string=$temp_row['current_roles'];

//echo "</br>DEBUG: $raw_curr_role_string </br>";
$role_array=explode(",", $raw_curr_role_string);
foreach($role_array as $role){
	$sub_role=explode("*", $role);
	if(isset($sub_role[1])){
		$curr_role_table[$sub_role[0]]=$sub_role[1];
	}
	else{
		$curr_role_table[$sub_role[0]]=1;
	}
}
$export = "";
$export.= "<table>\n";
$export.= "\t<tr>\n";
$export.= "\n\n<td>Roles</td>\n";
$export.= "\n\n<td>Max Number</td>\n\n\n<td>Current Number</td>\n";
$export.= "\t<tr>\n";
foreach($all_role_table as $key => $val){
	$export.= "\t<tr>\n\n\n<td>$key</td>\n\n\n<td>$val</td>\n\n\n<td>" . $curr_role_table[$key] . "</td>\n\t</tr>\n";
}
$export.= "</table>\n";
$export.= "<form name='refresh_form' action='./control_game.php' method='GET'>".
     "<input type='hidden' name='game_id' value='$game_id'/>".
     "<input type='submit' value='Refresh'>".
     "</form>".
     "<form name='reset_form' action='./reset_game.php' method='POST'>".
     "<input type='hidden' name='game_id' value='$game_id'/>".
     "<input type='submit' value='Reset game'/>".
     "</form>";
$export.= "<a href='./index.html'>Abandon game and return to index</a>";

echo $export;
$result->close();
?>
