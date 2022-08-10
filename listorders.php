<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>

<!--<!DOCTYPE HTML>
<html><head><title>list orders</title></head>
<body> -->

<?php
include "header.php";

include "menu.php";
//----------- page content starts here

?>

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
$query = 'SELECT orderID,orderDateTime,customerID,extras FROM orders ORDER BY orderID';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 

//prepare a query and send it to the server
$query1 = 'SELECT orderlineID,itemID,itemOrderAmount,orderID FROM orderline ORDER BY orderlineID';
$result1 = mysqli_query($DBC,$query1);
$rowcount1 = mysqli_num_rows($result1); 
?>
<h1>List orders</h1>
<h2><a href='placeorder.php'>[make an order]</a><a href="index.php">[Return to main page]</a></h2>

<table border="1">
<thead><tr><th>orderID</th><th>Customer</th><th>Date/time</th><th>extras</th><th>Action</th></tr></thead>

<?php

//makes sure we have food items
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['orderID'];	
      $pt = $row['customerID'];
	  echo '<tr><td>'.$row['orderID'].'</td><td>'.$pt.'</td><td>'.$row['orderDateTime'].'</td><td>'.$row['extras'].'</td>';
	  echo     '<td><a href="vieworder.php?id='.$id.'">[view]</a>';
	  echo         '<a href="editorder.php?id='.$id.'">[edit]</a>';
	  echo         '<a href="deleteorder.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No orders found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
?>

<thead><tr><th>orderlineID</th><th>itemID</th><th>item Amount</th><th> Order ID</th></tr></thead>

<?php
if ($rowcount1 > 0) {  
    while ($row1 = mysqli_fetch_assoc($result1)) {
	  $id1 = $row1['orderlineID'];	
      $pt1 = $row1['itemID'];
      $pt2 = $row1['orderID'];
	  echo '<tr><td>'.$row1['orderlineID'].'</td><td>'.$pt1.'</td><td>'.$row1['itemOrderAmount'].'</td><td>'.$pt2.'</td>';

      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No orderlines found!</h2>"; //suitable feedback


mysqli_free_result($result1);
mysqli_close($DBC); //close the connection once done
?>

</table>
<!--</body>
</html> -->

<?php
//----------- page content ends here
include "footer.php";
?>