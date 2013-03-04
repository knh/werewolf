<?php
header('Content-type: text/html; charset=utf-8'); 
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$game_id=$_POST['game_id'];
$query="SELECT * FROM werewolf_detail WHERE game_id=$game_id";
$mysqli=new mysqli("mydb.ics.purdue.edu",
    "gao118", "polaris", "gao118", "3306");
$result=$mysqli->query($query) or die("Query failed. ");
echo "Your game ID: $game_id</br>";
if(mysqli_num_rows($result) == 0){
  echo "Game ID $game_id doesn't exist. <script>window.setInterval(function(){document.location='./index.html'}, 2000)</script>";
  return;
}
else{
  $temp_row=mysqli_fetch_array($result); 
  if(isset($_POST['last_update'])) {
    //echo "last update: ". $_POST['last_update'] . "last reset:" .$temp_row['last_reset'] . "</br>";
    if($_POST['last_update'] >= strtotime($temp_row['last_reset'])){
      echo "Please wait for the host to start the new game then request a new role.</br>".
        "Your current role is " . $_POST['curr_role'] ."</br>".
        "<form name='new_role action='./join_game.php' method='POST'>".
        "<input type='hidden' name='curr_role' value='".$_POST['curr_role'] ."'/>".
        "<input type='hidden' name='game_id' value='$game_id' />".
        "<input type='hidden' name='last_update' value='" . $_POST['last_update'] ."'/>".
        "<input type='submit' value='Get new role'/>".
        "</form>";
      return;
    }
  }
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
  $remaining_role_array=array();
  foreach($total_role_array as $key => $val){
    if(isset($curr_role_array[$key])){
      $remaining_role_array[$key] = $val - $curr_role_array[$key];
    }else{
      $remaining_role_array[$key] = $val;
    }
  }


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
  $random_num=rand(0, count($temp_array) - 1);
  //echo "DEBUG: random: $random_num<br>";
  $user_role=$temp_array[$random_num];
  /*
  foreach($remaining_role_array as $key =>$val){
    echo "foreach: $key => $val </br>";
    while($random_num != 0 && $val != 0){
      echo "\t val: $val, ran: $random_num</br>";
      $val =$val-1;
      $random_num=$random_num - 1;
    }
    if($random_num == 0 && $val > 0){
      $user_role=$key;
      break;
    }
  }
  */
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
  $query="UPDATE werewolf_detail SET current_roles='$curr_role_raw' WHERE game_id=$game_id";
  //echo "query string: $query</br>";
  $mysqli->query($query);
}
?>
