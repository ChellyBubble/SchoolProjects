<?php
  session_start();

  // If the session vars aren't set, try to set them with a cookie
  if (!isset($_SESSION['Ssn'])) {
    if (isset($_COOKIE['Ssn']) && isset($_COOKIE['User_name']) && isset($_COOKIE['Admin_user'])) {
      $_SESSION['Ssn'] = $_COOKIE['Ssn'];
      $_SESSION['User_name'] = $_COOKIE['User_name'];
	  $_SESSION['Admin_user'] = $_COOKIE['Admin_user'];
    }
  }
?>