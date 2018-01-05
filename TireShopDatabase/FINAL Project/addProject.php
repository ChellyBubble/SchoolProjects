<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'New Project';
  require_once('header.php');

  require_once('connectvars.php');
  
  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['Ssn'])) {
    echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
    exit();
  }

  // Show the navigation menu
  require_once('navMenu.php');
  
  if (isset($_POST['submit'])) {
	$Pname = $_POST['Pname'];
	$Pnumber = $_POST['Pnumber'];
	$Plocation = $_POST['Plocation'];
	$Dnum = $_POST['Dnum'];
	$output_form = 'no';
  
    if (empty($Pname) || empty($Pnumber) || empty($Plocation) || empty($Dnum)) {
      // We know at least one of the input fields is blank 
      echo 'Please fill out all of the email information.<br />';
      $output_form = 'yes';
    }
  }
  else {
    $output_form = 'yes';
  }
  
  if (!empty($Pname) && !empty($Pnumber) && !empty($Plocation) && !empty($Dnum)) {
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die('Error connecting to MySQL server.');
  
  
	$query = "INSERT INTO A_PROJECT (Pname, Pnumber, Plocation, Dnum)  VALUES ('$Pname','$Pnumber', '$Plocation', '$Dnum')";
	mysqli_query($dbc, $query)
		or die('Error querying database.');

	echo 'Project added.';

	mysqli_close($dbc);
  }
  
  if($output_form == 'yes') {
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

	<fieldset>
		<table>
		<tr><td><label for="Pname">Name:</label></td>
		<td><input type="text" id="Pname" name="Pname" /></td></tr>
		<tr><td><label for="Pnumber">Number:</label></td>
		<td><input type="text" id="Pnumber" name="Pnumber" /></td></tr>
		<tr><td><label for="Plocation">Location:</label></td>
		<td><input type="text" id="Plocation" name="Plocation" /></td></tr>
		<tr><td><label for="Dnum">Department:</label></td>
	    <td><select id = "Dnum" name = "Dnum" />
	    <?php
		$dbc_3 = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
			or die('Error connecting to MySQL server.');
		$query_3 = "SELECT * FROM A_DEPARTMENT ORDER BY Dname";
		$data = mysqli_query($dbc_3, $query_3);
		while($row = mysqli_fetch_array($data)){
			echo '<option value= "' . $row['Dnumber'] . '" >' . $row['Dname'] . '</option><br />';
		}	
	    ?>
		</td></tr>
		</table>
	</fieldset><br />
    <input type="submit" name="submit" value="Add Project" />
  </form>

<?php
	}
?>

</body>
</html>