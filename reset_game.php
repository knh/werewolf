<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);
$game_id=$_POST['game_id'];
$query="UPDATE werewolf_detail SET last_reset=now(), current_roles=''  WHERE game_id='$game_id'";
$mysqli=new mysqli("mydb.ics.purdue.edu",
              "gao118", "polaris", "gao118", "3306");
if($mysqli->connect_errno){
  echo "connect failed." . $mysqli_connect_error;
}
$result=$mysqli->query($query) or die ("query failed.");
echo "Update successuful. Redirecting to original page.".
      "<script>window.setInterval(function(){document.location='./control_game.php?game_id=" .$game_id . "'}, 2000)</script>";
?>
