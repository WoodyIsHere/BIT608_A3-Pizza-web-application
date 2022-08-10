<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>

<!--<!DOCTYPE HTML>
<html><head><title>View order</title> </head>
 assumptions made are that the php code will interface with  selected order list and popuate the orderlines from SQL (see viewcustomer.php for an example) 
 <body>-->

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

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid orderID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM orders WHERE orderID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 

$query1 = 'SELECT * FROM orderline WHERE orderID='.$id;
$result1 = mysqli_query($DBC,$query1);
$rowcount1 = mysqli_num_rows($result1);
?>

 <h1>Order Details View</h1>
<h2><a href='listorders.php'>[Return to the orders listing]</a><a href='index.php'>[Return to the main page]</a></h2>

<?php

//makes sure we have the booking
if ($rowcount > 0) {  
   echo "<fieldset><legend>order detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>order ID:</dt><dd>".$row['orderID']."</dd>".PHP_EOL;
   echo "<dt>Customer ID:</dt><dd>".$row['customerID']."</dd>".PHP_EOL;
   echo "<dt>order Date/Time:</dt><dd>".$row['orderDateTime']."</dd>".PHP_EOL;
   echo "<dt>Extras:</dt><dd>".$row['extras']."</dd>".PHP_EOL;  
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No order found!</h2>"; //suitable feedback

if ($rowcount1 > 0){
    while($row1 = mysqli_fetch_assoc($result1)){
    echo "<fieldset><legend>orderline detail #".$row1['orderlineID']."</legend><dl>"; 
    echo "<dt>orderline ID:</dt><dd>".$row1['orderlineID']."</dd>".PHP_EOL;
    echo "<dt>item ID:</dt><dd>".$row1['itemID']."</dd>".PHP_EOL;
    echo "<dt>item order amount:</dt><dd>".$row1['itemOrderAmount']."</dd>".PHP_EOL;
    echo "<dt>order ID:</dt><dd>".$row1['orderID']."</dd>".PHP_EOL;  
    echo '</dl></fieldset>'.PHP_EOL;  }
}

mysqli_free_result($result); //free any memory used by the query
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
  