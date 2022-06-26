<?php

$errors = array();

if (empty($input_taskname) ) 
{
  $error_msg_1 = "Please enter the task performed!";
  array_push($errors, $error_msg_1);
}

if (empty($input_category)) 
{
  $error_msg_2 = "Please Choose The Category Of The Task Performed!";
  array_push($errors, $error_msg_2);
}

if ($input_date) 
{
  $error_msg_3 = "Please Select The Due Date Of The Task!";
  array_push($errors, $error_msg_3);
}

if ($input_time) 
{
  $error_msg_4 = "Please Select The Time Spent On The Task!";
  array_push($errors, $error_msg_4);
}

if ($responseData["success"] === false) 
{
  $error_msg_7 = "No robots please!!";
  array_push($errors, $error_msg_7);
}


?>