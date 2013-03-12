<?php
require_once('init.php');

$game_id =  (int) $_POST['game_id']; // prevent SQL injections
$query="SELECT * FROM werewolf_detail WHERE game_id = $game_id";
$result=$mysqli->query($query) or die("Query failed. ");
echo "Your game ID: $game_id</br>";

if(mysqli_num_rows($result) == 0){
	echo "Game ID $game_id doesn't exist. <script>window.setInterval(function(){document.location='./index.html'}, 2000)</script>";
	return;
}
else{
  $temp_row=mysqli_fetch_array($result); 
  if(isset($_POST['last_update'])) {
	// TODO: fix potential unchecked input XSS hole
	//echo "last update: ". $_POST['last_update'] . "last reset:" .$temp_row['last_reset'] . "</br>";
	// if the user requested a new role before the host reset the game, maintain he's current role.
	if($_POST['last_update'] >= strtotime($temp_row['last_reset'])){
		echo "Please wait for the host to start the new game then request a new role.</br>".
			"Your current role is " . $_POST['curr_role'] ."</br>".
			"<form name='new_role action='./join_game.php' method='POST'>".
			"<input type='hidden' name='curr_role' value='" . $_POST['curr_role'] . "'/>".
			"<input type='hidden' name='game_id' value='$game_id' />".
			"<input type='hidden' name='last_update' value='" . $_POST['last_update'] ."'/>".
			"<input type='submit' value='Get new role'/>".
			"</form>";
		return;
	}
  }
	//again, parseing the all role string.
	$raw_role_string=$temp_row['all_roles'];
	$role_array=preg_split("/(\r\n|\n|\r)/", $raw_role_string);
	$total_role_array=array();
	$total_player=0;
	foreach($role_array as $role){
		$sub_role=explode("*", $role);
	if(isset($sub_role[1])){
		$total_role_array[$sub_role[0]]=(int) $sub_role[1];
		$total_player+=$sub_role[1];
	}
	else{
		$total_role_array[$sub_role[0]]=(int) 1;
		$total_player+=1;
	}
  }
  $curr_player=0;
  // parsing the current role string from db.
  $raw_role_string=$temp_row['current_roles'];
  //echo "DEBUG: raw_role_string: $raw_role_string</br>";
  //echo "DEGUG: is empty: ". ($raw_role_string == '') . "</br>";
  $role_array=explode(",", $raw_role_string);
  $curr_role_array=array();
  if($role_array[0] != ''){
    foreach($role_array as $role){
      $sub_role=explode("*", $role);
      //echo "Debug: curr_role: " . $sub_role[0] . "</br>";
      if(isset($sub_role[1])){
        $curr_role_array[$sub_role[0]]=(int) $sub_role[1];
        $curr_player+=$sub_role[1];
      }
      else{
        $curr_role_array[$sub_role[0]]=(int) 1;
        $curr_player+=1;
      }
    }
  }
  if($curr_player >= $total_player){
    echo "Too many players. Please attend the next round.";
    return;
  }
  $remaining_role_array=array();// create the remaining role array and use that to assign a random role to a user.
  foreach($total_role_array as $key => $val){
    if(isset($curr_role_array[$key])){
      $remaining_role_array[$key] = $val - $curr_role_array[$key];
    }else{
      $remaining_role_array[$key] = $val;
    }
  }

  //create something like a linked list so that a random number can fall on one available role.
  $user_role;
  $temp_array=array();
  foreach($remaining_role_array as $key => $val){
    for($i = 0; $i < $val; $i ++){
      array_push($temp_array, $key);
    }
  }
  //echo "DEBUG temp_array: ";
  //foreach($temp_array as $var){
  //  echo "$var ";
  //}
  //echo "</br>";
  $random_num=rand(0, count($temp_array) - 1);//note in php, the range is inclusive.
  //echo "DEBUG: random: $random_num<br>";
  $user_role=$temp_array[$random_num];
  $remaining_role_array[$user_role] --;
  /*
  echo "<table>\n";
  echo "\t<tr>\n";
  echo "\n\n<td>Roles</td>\n";
  echo "\n\n<td>Max Number</td>\n\n\n<td>Remaining Number</td>\n";
  echo "\t<tr>\n";
  foreach($total_role_array as $key => $val){
    echo "\t<tr>\n";
    echo "\n\n<td>$key</td>\n";
    echo "\n\n<td>$val</td>\n";
    echo "\n\n<td>" . $remaining_role_array[$key] . "</td>\n";
    echo "\t</tr>\n";
  }
  echo "</table>\n";
  */
  echo "Your role: $user_role</br>".
    "<form name='new_role action='./join_game.php' method='POST'>".
    "<input type='hidden' name='curr_role' value='$user_role'/>".
    "<input type='hidden' name='game_id' value='$game_id' />".
    "<input type='hidden' name='last_update' value='" . strtotime("now") ."'/>".
    "<input type='submit' value='Get new role'/>".
    "</form>";
    //construct the new current role array, then parse it to a string
  if(isset($curr_role[$user_role])){
    $curr_role_array[$user_role] ++;
  }
  else{
    $curr_role_array[$user_role] = 1;
  }
  $curr_role_raw="";
  foreach($curr_role_array as $key => $val){
    $curr_role_raw = $curr_role_raw . "$key*$val,";
  }
  $curr_role_raw=substr($curr_role_raw, 0, strlen($curr_role_raw) - 1);
  $query="UPDATE werewolf_detail SET current_roles='$curr_role_raw' WHERE game_id=$game_id";//write back the new current role string after this user takes a role.
  //echo "query string: $query</br>";
  $mysqli->query($query);
}
?>
