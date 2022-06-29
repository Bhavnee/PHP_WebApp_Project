<!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Time Tracker - Tracks Time Spent On The Task </title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
   <link href="css/main.css" rel="stylesheet">
   <!-- add recaptcha library -->
   <script src='https://www.google.com/recaptcha/api.js' async defer></script>
 </head>

 <body class="submit">
   <nav class="navbar navbar-expand-lg navbar-light bg-light">
     <div class="container-fluid">
       <a class="navbar-brand" href="index.php">Time Tracker</a>
       <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
       </button>
       <div class="collapse navbar-collapse" id="navbarNav">
         <ul class="navbar-nav">
           <li class="nav-item">
             <a class="nav-link" href="submit.php">Submit</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="timerecord.php">Time Track Record</a>
           </li>
         </ul>
       </div>
     </div>
   </nav>
   <div class="container">
     <header>
       <h1> Time Tracker </h1>
       <h2> Track Time Spent On The Task! </h2>
     </header>
     <main>
       <?php
        //Step Three - add the ability for users to use the form to edit their information as well as add new information to the DB. To do this, we'll check if the user is editing and, if so, we'll populate the form with the values they want to change. When the user submits the form, their record will be updated. 

        //intialize variables 

        $taskname = null;
        $category = null;
        $date = null;
        $time = null;
        $user_id = null;
        $user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);



        //check if there is a user_id available in URL string, then prepare and execute a query that will return the information associated with the user_id selected and echo in the form 

        if (!empty($user_id) && $user_id !== false)
        {
            //connect to db
            require_once('connect.php');
            //set up sql query 
            $sql = "SELECT * FROM time WHERE user_id = :user_id;";
            //prepare query 
            $statement = $db->prepare($sql);
            //bind
            $statement->bindParam(':user_id', $user_id);
            //execute 
            $statement->execute();
            //use fetchAll method 
            $records = $statement->fetchAll();

            foreach ($records as $record) 
            {
              $taskname = $record['task_name'];
              $category = $record['task_category'];
              $date = $record['due_date'];
              $time = $record['time_spent'];
            }
            //close db connection 
            $statement->closeCursor();
        }

        //if the form has been submited, process the form information 
        if (isset($_POST['submit'])) {
          //check whether the recaptcha was checked by the user 
          if (!empty($_POST['g-recaptcha-response'])) {
            //create variables to store form data, using filter input to validate & sanitize 
            /*https://www.php.net/manual/en/filter.filters.sanitize.php*/
            $input_taskname = filter_input(INPUT_POST, 'tname', FILTER_SANITIZE_SPECIAL_CHARS);
            //should be category not tcategory - category is the value of the name attribute of your select element, tcategory is the id attribute value 
            $input_category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);

            /*$input_date = filter_input(INPUT_POST, 'tdate', FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT);
            in the format received via the datepicker, the date is not an integer, so we can remove the validation filter and just check that the user has provided a date 
            */

            $input_date = filter_input(INPUT_POST, 'tdate');

            $input_time = filter_input(INPUT_POST, 'ttime', FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT);
            
            //if editing, capture the id from the hidden input 
            $id = null;
            $id = filter_input(INPUT_POST, 'user_id');

            echo "<p> the date is $input_date; </p>"; 


            $secret = '6LfePG4gAAAAAASIYzASMEqTP1LD44Q5LVsv3NU9';
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);

            $responseData = json_decode($verifyResponse, true);


            require('validate.php');

            if (!empty($errors))
            {
              echo "<div class='error_msg alert alert-danger'>";
              foreach ($errors as $error) 
              {
                echo "<p>" . $error . "</p>";
              }
              echo "</div>";
            } else {

              try 
              {
                //connect to database 
                require_once('connect.php');

                // set up SQL command to insert data into table
                //if we have an id, we are editing (UPDATE), if not, we will be adding information to the table (INSERT)


                //this is a new tune we are adding to our app 
                // set up an SQL command to save the info 

                if (!empty($id))
                {
                  $sql = "UPDATE time SET task_name = :taskname, task_category = :category, due_date = :date, time_spent = :time WHERE user_id = :id";
                } 
                else 
                {
                  $sql = "INSERT INTO time (task_name, task_category, due_date, time_spent) VALUES (:taskname, :category, :date, :time);";
                }

                //call the prepare method of the PDO object, return PDOStatement Object
                $statement = $db->prepare($sql);

                //bind parameters
                $statement->bindParam(':taskname', $input_taskname);
                $statement->bindParam(':category', $input_category);
                $statement->bindParam(':date', $input_date);
                $statement->bindParam(':time', $input_time);
               
                //bind user id if needed 
                if (!empty($id)) 
                {
                  $statement->bindParam(':id', $id);
                }
                //execute the query 
                $statement->execute();

                //close the db connection 
                $statement->closeCursor();
                //redirect the user to the updated playlist page 
                header("Location: timerecord.php");
              }
               catch (PDOException $e) {
                echo "<p> Sorry! Something has gone wrong on our end! An email has been sent to our admin team </p>";
                $error_message = $e->getMessage();
                mail("jessicagilfillan@gmail.com", "TimeTracker", "An Error has occured " + $error_message);
                echo $error_message;
              }
            }
          } else {
            echo "<p class='alert alert-danger'> Please let us know you are not a robot! </p>";
          }
        }
        ?>
       <div class="row">
         <div class="col-md-6">
           <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="form">
             <!-- add hidden input with user id if editing -->
             <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
             <div class="form-group">
               <label for="tname"> Task Name </label>
               <input type="text" name="tname" class="form-control" id="tname" value="<?php echo $taskname; ?>" required>
             </div>
             <div class="form-group">
               <label for="tcategory"> Task Category </label>
               <select name="category" class="form-select form-select-lg form-control" id="tcategory">
                 <option selected>Choose Category Of Task</option>
                 <option value="read"> Reading </option>
                 <option value="code"> Coding </option>
                 <option value="ac"> Attend Classes </option>
                 <option value="assignments"> Assignments </option>
                 <option value="quiz"> Quizes </option>
                 <option value="db"> Discussion Board </option>
               </select>
             </div>
             <div>
               <label for="tdate"> Due Date </label>

               <input type="date" name="tdate" class="form-control" id="tdate" value="<?php echo $date; ?>" required>
             </div>
             <div class="form-group">
               <label for="ttime"> Time Spent On Task </label>
               <label for="from">From</label>
               <input type="time" name="ttime" class="form-control" id="ttime" value="<?php echo $time; ?>" required>
               <label for="to">To</label>
               <input type="time" name="ttime" class="form-control" id="ttime" value="<?php echo $time; ?>" required>
             </div>
          
             <!-- add the recpatcha widget -->
             <div class="g-recaptcha" data-sitekey="6LfePG4gAAAAADZ2lT_AlrPCCXB65KnudqxZMsdZ"></div>
             <input type="submit" name="submit" value="Submit" class="btn btn-primary">
           </form>
         </div>
         <div class="col-md-6">
           <img src="assets/music file2-07.svg" alt="listening to music illustration">
         </div>
       </div>
       <!--end row-->
     </main>
     <footer>
       <p> &copy; <?php echo getdate()['year']; ?> </p>
     </footer>
   </div>
   <!--end container-->
 </body>

 </html>