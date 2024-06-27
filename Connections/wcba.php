<?php
//-----Edit the following to suit server or user changes------
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "sdb";
//----------Do not edit below this line------
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
