<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<?php
include "header.php";

include "menu.php";
//----------- page content starts here

?>

<!--<!DOCTYPE HTML>
<html><head><title>View Booking</title> </head>
 <body> -->


<!--the assigment brief is hard to decipher so I added php code to this page before realising it wasnt needed for assignment 1. -->
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid bookingID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM booking WHERE bookingid='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>booking Details View</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='index.php'>[Return to the main page]</a></h2>
<?php

//makes sure we have the booking
if ($rowcount > 0) {  
   echo "<fieldset><legend>booking detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>BookingID:</dt><dd>".$row['bookingID']."</dd>".PHP_EOL;
   echo "<dt>CustomerID:</dt><dd>".$row['customerID']."</dd>".PHP_EOL;
   echo "<dt>Telephone:</dt><dd>".$row['telephone']."</dd>".PHP_EOL;
   echo "<dt>Booking Date:</dt><dd>".$row['bookingdate']."</dd>".PHP_EOL;
   echo "<dt>people:</dt><dd>".$row['people']."</dd>".PHP_EOL;  
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No booking found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>

<!--</body>
</html> -->

<?php
//----------- page content ends here
include "footer.php";
?>
  