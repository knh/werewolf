<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$player_num=$_POST['player_num'];
$raw_role_string=$_POST['role_list'];
$role_array=preg_split("/(\r\n|\n|\r)/", $raw_role_string);
$role_num_array=array();
$counter=0;
foreach($role_array as &$role){
  $sub_role = explode("*", $role);
  if(isset($sub_role[1])){
    $role=$sub_role[0];
    $role_num_array[$counter] = (int)$sub_role[1];
  }
  else{
    $role_num_array[$counter] = 1;
  }
  $counter++;
}
for($counter=0; $counter < count($role_array); $counter++){
  echo $role_array[$counter] . " * " . $role_num_array[$counter] ."</br>";
}
$link=mysql_connect('mydb.ics.purdue.edu:3306/gao118',
                    'gao118', 'polaris')
                  or die("Can not connect to db. " . mysql_error());
mysql_select_db('gao118') or die ('Can not select db.' . mysql_error());
$result=mysql_query($query) or die ("Query failed. " . mysql_error());
?>
