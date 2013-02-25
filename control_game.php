<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$game_id=$_GET['game_id'];
echo "Your game id is " . $_GET['game_id'];
$query="SELECT * FROM werewolf_detail WHERE game_id=" . $game_id;
$mysqli=new mysqli("mydb.ics.purdue.edu",
                    "gao118", "polaris", "gao118", "3306");
if($mysqli->connect_errno){
  echo "Connect failed. " . $mysqli_connect_error;
}
$result=$mysqli->query($query) or die ("Query failed.");
echo "<table>\n";
while ($row = $result->fetch_row()) {
  echo "\t<tr>\n";
  foreach($row as $field){
    echo "\n\n<td>$field</td>\n";
  }
  echo "\t</tr>\n";
}
echo "</table>\n";
$result->close();
?>
