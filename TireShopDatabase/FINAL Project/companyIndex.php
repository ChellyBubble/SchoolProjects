<?php
  // Start the session
  require_once('startsession.php');
  
  // Insert the page header
  $page_title = 'Main Menu';
  require_once('header.php');

  require_once('connectvars.php');

  // Show the navigation menu
  require_once('navMenu.php');

  // Connect to the database 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

  // Retrieve the project data from MySQL
  $query = "SELECT * FROM A_EMPLOYEE";
  $data = mysqli_query($dbc, $query);

  //echo '<h4>Options:</h4>';
  
  mysqli_close($dbc);
?>