<?php
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

//parse the all_role string
$temp_row=mysqli_fetch_array($result);
$raw_role_string=$temp_row['all_roles'];
$role_array=preg_split("/(\r\n|\n|\r)/", $raw_role_string);
$role_num_array=array();
$counter=0;
$player_num=0;
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

//print them out.
echo "<table>\n";
echo "\t<tr>\n";
    echo "\n\n<td>Roles</td>\n";
    echo "\n\n<td>Max Number</td>\n\n\n<td>Current Number</td>\n";
echo "\t<tr>\n";
for($i=0; $i < count($role_num_array); $i++) {
  echo "\t<tr>\n";
    echo "\n\n<td>$role_array[$i]</td>\n";
    echo "\n\n<td>$role_num_array[$i]</td>\n";
  echo "\t</tr>\n";
}
echo "</table>\n";
$result->close();
?>
