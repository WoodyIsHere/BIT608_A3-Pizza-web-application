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
<html><head><title>list bookings</title></head>
the assigment brief is hard to decipher so I added php code to this page before realising it wasnt needed for assignment 1.
<body> -->
<h1>List Bookings</h1>
<h2><a href='makebookings.php'>[make a booking]</a><a href="index.php">[Return to main page]</a></h2>

<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT bookingID,customerID,telephone,bookingdate,people FROM booking ORDER BY bookingID';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>

<table border="1">
<thead><tr><th>bookingID</th><th>CustomerID</th><th>telephone</th><th>booking date</th><th>people</th><th>Action</th></tr></thead>
<?php

//makes sure we have booking
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['bookingID'];
      $cus_id = $row['customerID'];
      $tele = $row['telephone'];	
      $pt = $row['bookingdate'];
      $ppl = $row['people'];
	  echo '<tr><td>'.$row['bookingID'].'</td><td>'.$cus_id.'</td><td>'.$tele.'</td><td>'.$pt.'</td><td>'.$ppl.'</td>';
	  echo     '<td><a href="viewbooking.php?id='.$id.'">[view]</a>';
	  echo         '<a href="editbooking.php?id='.$id.'">[edit]</a>';
	  echo         '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No bookings found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>

<!--</body>-->

<?php
//----------- page content ends here
include "footer.php";
?>
