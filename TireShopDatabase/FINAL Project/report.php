<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Total Project Hours by Department';
  require_once('header.php');

  require_once('connectvars.php');
  
  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['Ssn'])) {
    echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
    exit();
  }
  
  // Show the navigation menu
  require_once('navMenu.php');
  
  // Make sure the user is  an admin user before going any further.
  if ($_SESSION['Admin_user'] == '0') {
    echo '<p class="login">User does not have access this page.</p>';
    exit();
  }
  
  // Connect to the database 
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  $query = "SELECT * FROM A_DEPARTMENT ORDER BY Dname";
  $data = mysqli_query($dbc, $query);
  
  while ($row = mysqli_fetch_array($data)) { 
  
  $Dno = $row['Dnumber'];
  
  // Retrieve the score data from MySQL
  $query_2 = "SELECT * FROM A_PROJECT WHERE Dnum = '" . $Dno . "' ORDER BY Pnumber";
  $data_2 = mysqli_query($dbc, $query_2);

  // Loop through the array of score data, formatting it as HTML 
  echo '<fieldset>';
  echo '<legend>'.  $row['Dname'] . '</legend>';
  echo '<table>';
  echo '<tr><th>Number</th><th>Name</th><th>Total Hours</th></tr>';
  while ($row = mysqli_fetch_array($data_2)) { 

	$query_4 = "SELECT SUM(Hours) AS THours  FROM A_WORKS_ON WHERE Pno = '" . $row['Pnumber'] . "'";
	$data_4 = mysqli_query($dbc, $query_4);
	$row_4 = mysqli_fetch_array($data_4);
	
    echo '<tr class="important"><td><strong>' . $row['Pnumber'] . '</strong></td>';
	echo '<td>' . $row['Pname'] . '</td>';
	echo '<td>';
	if (!empty($row_4['THours'])){ 
		echo $row_4['THours'];
	} 
	else{ 
		echo 'None';
	} 
	echo '</td>';
  }
  echo '</table>';
  echo '<br /><br />';
  $query_5 = "SELECT SUM(Hours) AS DHours  FROM A_WORKS_ON, A_PROJECT WHERE Pno = Pnumber AND Dnum = '" . $Dno . "'";
  $data_5 = mysqli_query($dbc, $query_5);
  $row_5 = mysqli_fetch_array($data_5);
  echo '<br /><strong>GRAND TOTAL  ';
	if (!empty($row_5['DHours'])){ 
		echo $row_5['DHours'];
	} 
	else{ 
		echo 'None';
	} 
  echo '<br /></strong>';
  echo '</fieldset>';
  echo '<br />';
  echo '<br />';
  }
  
  mysqli_close($dbc);
?>

</body> 
</html>