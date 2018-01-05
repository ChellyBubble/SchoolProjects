<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Administration Projects';
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

  // Retrieve the score data from MySQL
  $query = "SELECT * FROM A_PROJECT ORDER BY Pnumber";
  $data = mysqli_query($dbc, $query);

  // Loop through the array of score data, formatting it as HTML 
  echo '<table>';
  echo '<tr><th>Name</th><th>Number</th><th>Action</th></tr>';
  while ($row = mysqli_fetch_array($data)) { 
    // Display the score data
    echo '<tr class="scorerow"><td><strong>' . $row['Pname'] . '</strong></td>';
    echo '<td>' . $row['Pnumber'] . '</td>';
    echo '<td><a href="removeProject.php?Pnumber=' . $row['Pnumber'] . '&amp;Pname=' . $row['Pname'] .
      '&amp;Plocation=' . $row['Plocation'] . '&amp;Dnum=' . $row['Dnum'] . '">Remove</a>';
    if ($row['Papproved'] == '0') {
      echo ' / <a href="approveProject.php?Pnumber=' . $row['Pnumber'] . '&amp;Pname=' . $row['Pname'] .
        '&amp;Plocation=' . $row['Plocation'] . '&amp;Dnum=' . $row['Dnum'] . '">Approve</a>';
    }
    echo '</td></tr>';
  }
  echo '</table>';

  mysqli_close($dbc);
?>
	<p><a href="addProject.php">Add New Project</a></p>

</body> 
</html>
