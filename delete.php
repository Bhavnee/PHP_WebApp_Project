<?php


$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT); 

if(!empty($user_id) && $user_id !== false) {

    require_once('connect.php');     
    $sql = "DELETE FROM time WHERE user_id = :user_id"; 
    $statement = $db->prepare($sql); 
    $statement->bindParam(':user_id',$user_id ); 
    $statement->execute(); 
    $statement->closeCursor(); 
    header("Location: timerecord.php"); 

}

