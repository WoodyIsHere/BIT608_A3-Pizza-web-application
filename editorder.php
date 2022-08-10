<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>

<!--<!DOCTYPE HTML>
<html><head><title>edit order</title></head>

<body>-->

<?php
include "header.php";

include "menu.php";
//----------- page content starts here

?>
    


<h1>edit order</h1>
<h2><a href='listorders.php'>[return to orders listing]</a><a href="index.php">[Return to main page]</a></h2>

<h1>Pizza order </h1>

<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
  exit; //stop processing the page further
};

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the itemid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid order ID 1</h2>"; //simple error feedback
        exit;
    } 
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
//validate incoming data - only the first field is done for you in this example - rest is up to you do
    
//refer to additems for extend validation examples
//itemID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid order ID 2'; //append error message
       $id = 0;  
    }   
//pizza
       $pizza = cleanInput($_POST['ordertime']); 
//description
       $description = cleanInput($_POST['extras']);        
       
    
//save the item data if the error flag is still clear and item id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE orders SET orderDateTime=?,extras=? WHERE orderID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'ssi', $pizza, $description, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>order details updated.</h2>";     
//        header('Location: http://localhost/bit608/listitems.php', true, 303);      
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
//locate the food item to edit by using the itemID
//we also include the item ID in our form for sending it back for saving the data
$query = 'SELECT orderlineID,itemID,itemOrderAmount,orderID FROM orderline WHERE orderID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);

?>

<form method="POST" action="editorder.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
   <p>
   <label for="ordertime">Order Date and time</label>
        <input type="datetime-local" id="ordertime" name="ordertime" min="2022-04-01T00:00"  max="2023-04-01T00:00" required> 
  </p> 
  <p>
  <label for="extras">extras: </label>
    <input type="text" id="extras" name="extras" minlength="1" maxlength="200" required> 
  </p>  
  
   <input type="submit" name="submit" value="Update">
   <a href="listorders.php">[Cancel]</a>   
 </form>
<?php 
} else { 
  echo "<h2>order not found with that ID (no order ID)</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>

<!--</body>
</html>-->
<?php
//----------- page content ends here
include "footer.php";
?>