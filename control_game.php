<?php
header('Content-type: text/html; charset=utf-8'); 
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$game_id=$_GET['game_id'];
echo "Your game id is " . $_GET['game_id'];
//Query the database
$query="SELECT * FROM werewolf_detail WHERE game_id=" . $game_id;
$mysqli=new mysqli("mydb.ics.purdue.edu",
    "gao118", "polaris", "gao118", "3306");
if($mysqli->connect_errno){
  echo "Connect failed. " . $mysqli_connect_error;
}
$result=$mysqli->query($query) or die ("Query failed.");

//parse the all_role string. The final result stores in all_role_table. 
//TODO: Refactor it after a better understanding of php.
$temp_row=mysqli_fetch_array($result);
$raw_role_string=$temp_row['all_roles'];
$role_array=preg_split("/(\r\n|\n|\r)/", $raw_role_string);
$role_num_array=array();
$counter=0;
$player_num=0;
$all_role_table=array();
foreach($role_array as &$role){
  //echo "</br>DEBUG: role: $role</br>";
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


//print them out.
echo "<table>\n";
echo "\t<tr>\n";
    echo "\n\n<td>Roles</td>\n";
    echo "\n\n<td>Max Number</td>\n\n\n<td>Current Number</td>\n";
echo "\t<tr>\n";
foreach($all_role_table as $key => $val){
  echo "\t<tr>\n";
    echo "\n\n<td>$key</td>\n";
    echo "\n\n<td>$val</td>\n";
    echo "\n\n<td>" . $curr_role_table[$key] . "</td>\n";
  echo "\t</tr>\n";
}
echo "</table>\n";
echo "<form name='refresh_form' action='./control_game.php' method='GET'>".
     "<input type='hidden' name='game_id' value='$game_id'/>".
     "<input type='submit' value='Refresh'>".
     "</form>".
     "<form name='reset_form' action='./reset_game.php' method='POST'>".
     "<input type='hidden' name='game_id' value='$game_id'/>".
     "<input type='submit' value='Reset game'/>".
     "</form>";
echo "<a href='./index.html'>Abandon game and return to index</a>";
$result->close();
?>
