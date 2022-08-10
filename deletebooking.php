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


//the data was sent using a form therefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) 
    and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
//orderID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) 
        and is_integer(intval($_POST['id']))) {
       $id = $_POST['id']; 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID 2 '; //append error message
       $id = 0;  
    }        
    
//save the member data if the error flag is still clear and member id is > 0
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM booking WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'i', $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>booking details deleted.</h2>";     
        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 




?>

<!--<!DOCTYPE HTML>
 assumptions made are that the php code will interface with  selected booking list and popuate the booking fields from data pulled from sql
<html><head> </head>
 <body>-->
<title>Delete a booking</title>
 <h1>Delete a booking</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='index.php'>[Return to the main page]</a></h2>

<?php

//makes sure we have the booking
if ($rowcount > 0) {  
   echo "<fieldset><legend>booking detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>booking ID:</dt><dd>".$row['bookingID']."</dd>".PHP_EOL;
   echo "<dt>Customer ID:</dt><dd>".$row['customerID']."</dd>".PHP_EOL;
   echo "<dt>telephone number:</dt><dd>".$row['telephone']."</dd>".PHP_EOL;
   echo "<dt>booking date/time:</dt><dd>".$row['bookingdate']."</dd>".PHP_EOL; 
   echo "<dt>number of guests:</dt><dd>".$row['people']."</dd>".PHP_EOL;  
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No booking found!</h2>"; //suitable feedback


?>

<form method="POST" action="deletebooking.php">
     <h2>Are you sure you want to delete this booking?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href="index.php">[Cancel]</a>
     </form>
        
     <?php    
mysqli_free_result($result); //free any memory used by the query
mysqli_free_result($result1);
mysqli_close($DBC); //close the connection once done
?>
        
            
    <!--</body>
</html>-->

<?php
//----------- page content ends here
include "footer.php";
?>
