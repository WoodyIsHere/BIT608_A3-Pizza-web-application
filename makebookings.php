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
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'submit')) {
//if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
    include "config.php"; //load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    
    $error = 0; //clear our error flag
    $msg = 'Error: ';

    //booking time name
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

    //customer ID
    if ($_SESSION['loggedin'] = 1) {
       $fn = ($_SESSION['userid']);            
       $customerID = $fn; //check length and clip if too big   
      } else {
        $error++; //bump the error flag
        $msg .= 'Invalid customer ID  '; //append eror message
        $customerID = '';  
     }   

     // contact number
     if(isset($_POST['contactnumber']) and !empty($_POST['contactnumber'])){
       $fn = $_POST['contactnumber'];
       $contactNumber = $fn;
     } else {
       $error++;
       $msg.= 'invalid contact number';
       $contactNumber = '';
     }

     //save the item data if the error flag is still clear
    if ($error == 0) {
      $query = "INSERT INTO booking (customerID,telephone,bookingdate,people) VALUES (?,?,?,?)";
      $stmt = mysqli_prepare($DBC,$query); //prepare the query

      mysqli_stmt_bind_param($stmt, 'issi', $customerID, $contactNumber, $bookingTime, $peopleNumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<h2>New booking added to the list</h2>";        
      } else { 
        echo "<h2>$msg</h2>".PHP_EOL;
      }
          
     mysqli_close($DBC); //close the connection once done
  }
?>

<!--<!DOCTYPE HTML>
 assumptions made are that the php code will interface with the user input and INSERT information to SQL database. 

<html><head>-->


<!-- flatpickr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">


    <title>Make booking</title> </head>

 <!--<body> -->
     <!-- script to implement flatpickr -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>

     <h1>Make a booking</h1>
     <h2><a href='listbookings.php'>[return to booking list]</a><a href="index.php">[Return to main page]</a>

     <h1>Booking for test</h1>
     
     
    

     <form method="POST" id="selectBooking">
         <div>
        <label for="bookingtime">Booking Date and time</label>
        <input type="datetime-local" id="bookingtime" name="bookingtime" placeholder="please select a date" required>
</div>
        
    

        <div>
        <label for="partysize">Party Size (#people, 1 - 10)</label>
        <select name="partysize" id="partysize" pattern="[1-10]" required>
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
        </div>

        <div>
        <label for="contactnumber">Contact number:</label>
        <input type="tel" id="contactnumber" name="contactnumber" placeholder="###-###-####" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
        </div>
        <p><label>format is 123-456-7890</label></p>

        
</form>

        <input type="submit" name="submit" value="submit" form ="selectBooking">
        <a href="index.php">[Cancel]</a>




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
//----------- page content ends here
include "footer.php";
?>
