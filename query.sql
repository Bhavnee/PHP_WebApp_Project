CREATE TABLE `time`
(
    `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(100) NOT NULL,
  `task_category` varchar (100) NOT NULL,
  `due_date` varchar (100) NOT NULL,
  `time_spent` varchar (100) NOT NULL,
  PRIMARY KEY (user_id)
);