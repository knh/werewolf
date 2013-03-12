<?php
require_once('init.php');

$game_id=(int) $_POST['game_id']; //prevent SQL injection attacks

//wipe out the current role field and update last reset timestamp so that user can refresh and get a new role.
$query="UPDATE werewolf_detail SET last_reset=now(), current_roles=NULL  WHERE game_id='$game_id'";

$result=$mysqli->query($query) or die ("query failed.");

@header("Location: control_game.php?game_id=" . $game_id); //Use header redirection first!

echo "Update successuful. Redirecting to original page.
<script>window.setInterval(function(){document.location='./control_game.php?game_id=" .$game_id . "'}, 2000)</script>";
?>
