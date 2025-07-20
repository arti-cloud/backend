<?php 
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'User_Crud';

$connection = new mysqli ($host, $user, $password, $db);

if ($connection->connect_error){
    die (json_encode(['error'=> 'Database connection Failed: ' .  $connection->connect_error]));
}
?>