<?php

try 
{ 
    $dsn = 'mysql:host=172.31.22.43;dbname=Bhavneet200504132';
  
    $username = 'Bhavneet200504132'; 

    $password = 'RbqCxNnfI4';
    $db = new PDO($dsn, $username, $password);
   
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
   $error_message = $e->getMessage(); 
   echo "<p> Whoops! Our bad! Something happened while trying to connect. It was this -  $error_message </p>"; 
}
?>