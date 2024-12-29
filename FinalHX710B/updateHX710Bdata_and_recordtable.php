<?php
require 'database.php';

//---------------------------------------- Condition to check that POST value is not empty.
if (!empty($_POST)) {
  //........................................ keep track POST values
  $id = $_POST['id'];
  $pressureInput = $_POST['pressureInput'];
  $pressureOutput = $_POST['pressureOutput'];
  $pressureSelisih = $_POST['pressureSelisih'];
  $status_read_sensor_hx710b_input = $_POST['status_read_sensor_hx710b_input'];
  $status_read_sensor_hx710b_output = $_POST['status_read_sensor_hx710b_output'];
  //........................................

  //........................................ Get the time and date.
  date_default_timezone_set("Asia/Jakarta"); // Look here for your timezone : https://www.php.net/manual/en/timezones.php
  $tm = date("H:i:s");
  $dt = date("Y-m-d");
  //........................................

  //........................................ Updating the data in the table.
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "UPDATE esp32_table_hx710b_update SET pressureInput = ?, pressureOutput = ?, pressureSelisih = ?,  status_read_sensor_hx710b_input = ?, status_read_sensor_hx710b_output = ?, time = ?, date = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($pressureInput, $pressureOutput, $pressureSelisih, $status_read_sensor_hx710b_input, $status_read_sensor_hx710b_output, $tm, $dt, $id));
  Database::disconnect();
  //........................................ 

  //........................................ Entering data into a table.
  $id_key;
  $board = $_POST['id'];
  $found_empty = false;

  $pdo = Database::connect();

  //:::::::: Process to check if "id" is already in use.
  while ($found_empty == false) {
    $id_key = generate_string_id(10);
    $sql = 'SELECT * FROM esp32_table_hx710b_record WHERE id="' . $id_key . '"';
    $q = $pdo->prepare($sql);
    $q->execute();

    if (!$data = $q->fetch()) {
      $found_empty = true;
    }
  }
  //::::::::

  //:::::::: The process of entering data into a table.
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "INSERT INTO esp32_table_hx710b_record (id,board,pressureInput,pressureOutput,pressureSelisih,status_read_sensor_hx710b_input,status_read_sensor_hx710b_output,time,date) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($id_key, $board, $pressureInput, $pressureOutput, $pressureSelisih, $status_read_sensor_hx710b_input, $status_read_sensor_hx710b_output, $tm, $dt));
  //::::::::

  Database::disconnect();
  //........................................ 
}
//---------------------------------------- 

//---------------------------------------- Function to create "id" based on numbers and characters.
function generate_string_id($strength = 16)
{
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $input_length = strlen($permitted_chars);
  $random_string = '';
  for ($i = 0; $i < $strength; $i++) {
    $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
    $random_string .= $random_character;
  }
  return $random_string;
}
//---------------------------------------- 
