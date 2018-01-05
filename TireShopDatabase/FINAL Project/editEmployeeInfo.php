<?php
  // Start the session
  require_once('startsession.php');

  // Insert the page header
  $page_title = 'Edit Personal Information';
  require_once('header.php');

  require_once('appvars.php');
  require_once('connectvars.php');

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['Ssn'])) {
    echo '<p class="login">Please <a href="login.php">log in</a> to access this page.</p>';
    exit();
  }

  // Show the navigation menu
  require_once('navMenu.php');

  // Connect to the database
  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if (isset($_POST['submit'])) {
    // Grab the profile data from the POST
    $Address = mysqli_real_escape_string($dbc, trim($_POST['Address']));
	$Email = mysqli_real_escape_string($dbc, trim($_POST['Email']));
	$User_name = mysqli_real_escape_string($dbc, trim($_POST['User_name']));
	$old_picture = mysqli_real_escape_string($dbc, trim($_POST['old_picture']));
    $new_picture = mysqli_real_escape_string($dbc, trim($_FILES['new_picture']['name']));
    $new_picture_type = $_FILES['new_picture']['type'];
    $new_picture_size = $_FILES['new_picture']['size']; 
	list($new_picture_width, $new_picture_height) = getimagesize($_FILES['new_picture']['tmp_name']);
    $error = false;

	if (!empty($new_picture)) {
      if ((($new_picture_type == 'image/gif') || ($new_picture_type == 'image/jpeg') || ($new_picture_type == 'image/pjpeg') ||
        ($new_picture_type == 'image/png')) && ($new_picture_size > 0) && ($new_picture_size <= MM_MAXFILESIZE) &&
        ($new_picture_width <= MM_MAXIMGWIDTH) && ($new_picture_height <= MM_MAXIMGHEIGHT)) {
        if ($_FILES['file']['error'] == 0) {
          // Move the file to the target upload folder
          $target = MM_UPLOADPATH . basename($new_picture);
          if (move_uploaded_file($_FILES['new_picture']['tmp_name'], $target)) {
            // The new picture file move was successful, now make sure any old picture is deleted
            if (!empty($old_picture) && ($old_picture != $new_picture)) {
              @unlink(MM_UPLOADPATH . $old_picture);
            }
          }
          else {
            // The new picture file move failed, so delete the temporary file and set the error flag
            @unlink($_FILES['new_picture']['tmp_name']);
            $error = true;
            echo '<p class="error">Sorry, there was a problem uploading your picture.</p>';
          }
        }
      }
      else {
        // The new picture file is not valid, so delete the temporary file and set the error flag
        @unlink($_FILES['new_picture']['tmp_name']);
        $error = true;
        echo '<p class="error">Your picture must be a GIF, JPEG, or PNG image file no greater than ' . (MM_MAXFILESIZE / 1024) .
          ' KB and ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . ' pixels in size.</p>';
      }
    }
	
    // Update the profile data in the database
    if (!$error) {
      if (!empty($Address) && !empty($Email) && !empty($User_name) ) {
        // Only set the picture column if there is a new picture
      if (!empty($new_picture)) {
			$query = "UPDATE A_EMPLOYEE SET Address = '$Address', Email = '$Email', User_name = '$User_name', Picture = '$new_picture' WHERE Ssn = '" . $_SESSION['Ssn'] . "'";
        }
        else {
			$query = "UPDATE A_EMPLOYEE SET Address = '$Address', Email = '$Email', User_name = '$User_name' WHERE Ssn = '" . $_SESSION['Ssn'] . "'";
        }
	  
	  
        mysqli_query($dbc, $query);

        // Confirm success with the user
        echo '<p>Your information has been successfully updated. </p>';

        mysqli_close($dbc);
        exit();
      }
      else {
        echo '<p class="error">You must enter all of the information.</p>';
      }
    }
  } // End of check for form submission
  else {
    // Grab the profile data from the database
    $query = "SELECT * FROM A_EMPLOYEE WHERE Ssn = '" . $_SESSION['Ssn'] . "'";
    $data = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($data);

    if ($row != NULL) {
      $Address = $row['Address'];
	  $Email = $row['Email'];
	  $User_name = $row['User_name'];
	  
    }
    else {
      echo '<p class="error">There was a problem accessing your information.</p>';
    }
	
	//Show the user their Supervisor
		$query_2 = "SELECT Fname, Lname  FROM A_EMPLOYEE WHERE Ssn = '" . $row['Super_ssn'] . "'";
		$data_2 = mysqli_query($dbc, $query_2);
		$row_2 = mysqli_fetch_array($data_2);
		
		// Show the user their own Department
		$query_3 = "SELECT Dname  FROM A_DEPARTMENT WHERE Dnumber = '" . $row['Dno'] . "'";
		$data_3 = mysqli_query($dbc, $query_3);
		$row_3 = mysqli_fetch_array($data_3);
	
  }

  mysqli_close($dbc);
?>

  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MM_MAXFILESIZE; ?>" />
    <fieldset>
      <legend>Personal Information</legend>
	  <table>
	  <tr><td class="label">First Name:</td><td><?php echo $row['Fname'] ?> </td></tr>
	  <tr><td class="label">Middle Initial:</td><td><?php echo $row['Minit'] ?></td></tr>
	  <tr><td class="label">Last name:</td><td><?php echo $row['Lname'] ?></td></tr>
	  <tr><td class="label">Gender:</td><td><?php if($row['Sex'] == 'M'){ echo 'Male';}else {echo 'Female';}?></td></tr>
	  <tr><td class="label">Date of Birth:</td><td><?php echo $row['Bdate'] ?></td></tr>
      <tr><td><label for="Address">Address:</label></td>
      <td><input type="text" id="Address" name="Address" value="<?php if (!empty($Address)) echo $Address; ?>" /><br /></td></tr>
	  <tr><td class="label">Salary:</td><td><?php echo $row['Salary'] ?></td></tr>
	  <tr><td class="label">Supervisor:</td><td><?php if ($row['Super_ssn'] == NULL){ echo 'N/A';} else{echo $row_2['Fname'] .  ' ' . $row_2['Lname'];} ?></td></tr>
	  <tr><td class="label">Department:</td><td><?php echo $row_3['Dname'] ?></td></tr>
	  <tr><td><label for="Email">Email:</label></td>
      <td><input type="text" id="Email" name="Email" value="<?php if (!empty($Email)) echo $Email; ?>" /><br /></td></tr>
	  <tr><td><label for="User_name">User Name:</label></td>
      <td><input type="text" id="User_name" name="User_name" value="<?php if (!empty($User_name)) echo $User_name; ?>" /><br /></td></tr>
	  <input type="hidden" name="old_picture" value="<?php if (!empty($old_picture)) echo $old_picture; ?>" />
      <tr><td><label for="new_picture">Picture:</label></td>
      <td><input type="file" id="new_picture" name="new_picture" />
      <?php if (!empty($old_picture)) {
        echo '<img class="profile" src="' . MM_UPLOADPATH . $old_picture . '" alt="Profile Picture" />';
      } ?>
	  </td></tr>
	  </table>
	  
    </fieldset>
    <input type="submit" value="Save Profile" name="submit" />
  </form>