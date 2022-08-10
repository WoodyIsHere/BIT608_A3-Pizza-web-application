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
        echo "<h2>Invalid booking ID 1</h2>"; //simple error feedback
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
       $msg .= 'Invalid booking ID 2'; //append error message
       $id = 0;  
    }   
//booking date/time

    if (isset($_POST['bookingtime']) and !empty($_POST['bookingtime'])) {
        $fn = date("Y-m-d H:i:s", strtotime($_POST['bookingtime'])); 
        $bookingTime = $fn; //check length and clip if too big
          //we would also do context checking here for contents, etc       
    } else {
        $error++; //bump the error flag
        $msg .= 'Invalid booking time  '; //append eror message
        $bookingTime = '';  
    } 


    //people number
    if(isset($_POST['partysize']) and !empty($_POST['partysize'])){
      $fn = $_POST['partysize']; 
      $peopleNumber = $fn;
    } else {
      $error++;
      $msg.= 'invalid party number ';
      $peopleNumber = '';
    }       
       
//telephone number
        $telephone = cleanInput($_POST['contactnumber']);
    
//save the item data if the error flag is still clear and item id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE booking SET telephone=?,bookingdate=?,people=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'ssii', $telephone, $bookingTime, $peopleNumber, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>booking details updated.</h2>";     
//        header('Location: http://localhost/bit608/listitems.php', true, 303);      
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
//locate the food item to edit by using the itemID
//we also include the item ID in our form for sending it back for saving the data
$query = 'SELECT bookingID,customerID,telephone,bookingdate,people FROM booking WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);

?>

<title>Edit a booking</title>
<!-- flatpickr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">

<!-- assumptions made is that upon clicking edit button the booking date and time, party size and contact numnber will be populated with the 
corresponding data assigned to that bookingID in the database.-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 
 <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>

 <h1>Edit a booking</h1>
<h2><a href='listbookings.php'>[Return to the booking listing]</a><a href='index.php'>[Return to the main page]</a></h2>

<h1>Booking for test</h1>
     
     <form method="POST" action="editbooking.php">
     <input type="hidden" name="id" value="<?php echo $id;?>">
         <div>
        <label for="bookingtime">Booking Date and time</label>
        <input type="datetime-local" id="bookingtime" name="bookingtime" min="2022-04-01T00:00"  max="2023-04-01T00:00" placeholder="Please enter a date" required>
        </div>
    

        <p>
        <label for="partysize">Party Size (#people, 1 - 10)</label>
        <select name="partysize" id="partysize" pattern="[1-10]" required>
            <option value="">please select an option</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
        </p>

    
        <label for="contactnumber">Contact number:</label>
        <input type="tel" id="contactnumber" name="contactnumber" placeholder="###-###-####" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
        <p><label>format is 123-456-7890</label></p>

        <p>
        <input type="submit" name="submit" value="Update">
        <a href="index.php">[Cancel]</a> 
        </p>
</form>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
  


<!-- script for flatpickr to arrange date/time to correct format -->
<script> 

$("#bookingtime").flatpickr({
    enableTime: true,
    dateFormat: "Y-m-d H:i",
});
</script>

<!--</body>
</html> -->
<?php 
} else { 
  echo "<h2>booking not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
<?php
//----------- page content ends here
include "footer.php";
?>