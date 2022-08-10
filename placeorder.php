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
<!--!DOCTYPE HTML>
<html><head><title>Place order</title></head> -->

<script
  src="https://code.jquery.com/jquery-2.2.0.min.js"
  integrity="sha256-ihAoc6M/JPfrIiIeayPE9xjin4UWjsx2mjW/rtmxLM4="
  crossorigin="anonymous"></script>

<!--<body> -->


<?php
echo "<pre>"; var_dump($_POST); echo "</pre>";
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

//validate incoming data - only the first field is done for you in this example - rest is up to you do


//order time name
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    if (isset($_POST['ordertime']) and !empty($_POST['ordertime'])) {
       $fn = date("Y-m-d H:i:s", strtotime($_POST['ordertime'])); 
       $orderTime = $fn; //check length and clip if too big
       //we would also do context checking here for contents, etc       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid order time  '; //append eror message
       $orderTime = '';  
    } 

//extras
    if (isset($_POST['extras']) and !empty($_POST['extras']) and is_string($_POST['extras'])) {
       $fn = cleanInput($_POST['extras']);        
       $extras = (strlen($fn)>200)?substr($fn,1,200):$fn; //check length and clip if too big   
       //we would also do context checking here for contents, etc  
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid extras  '; //append eror message
       $extras = '';  
    }        
//customer ID
    if ($_SESSION['loggedin'] = 1) {
       $fn = ($_SESSION['userid']);            
       $customerID = $fn; //check length and clip if too big   
       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid customer ID  '; //append eror message
       $CustomerID = '';  
    }     
      
    //orderline item ID
    if(isset($_POST['myInputs']) and !empty($_POST['myInputs'])){
    $fn = $_POST['myInputs']; 
    $itemID = $fn;
    } else {
      $error++;
      $msg.= 'invalid item ID ';
      $itemID = '';
    }

    //amount for orderline
    if(isset($_POST['count']) and !empty($_POST['count'])){
      $fn = cleanInput($_POST['count']); 
      $itemAmount = $fn;
      } else {
        $error++;
        $msg.= 'invalid count ';
        $itemAmount = '';
      }

      
//save the item data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO orders (orderDateTime,customerID,extras) VALUES (?,?,?)";
        $query1 = "INSERT INTO orderline (itemID,itemOrderAmount,orderID) VALUES (?,?,?)";
      
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        $stmt1 = mysqli_prepare($DBC,$query1);
        
        

        mysqli_stmt_bind_param($stmt, 'sis', $orderTime, $customerID, $extras);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $last_id = mysqli_insert_id($DBC); 

        mysqli_stmt_bind_param($stmt1, 'iii', $itemID, $itemAmount, $last_id);
        mysqli_stmt_execute($stmt1); 
        mysqli_stmt_close($stmt1);

          

        echo "<h2>New order added to the list</h2>";        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }
        
   mysqli_close($DBC); //close the connection once done
}
?>




<h1>Place order</h1>
<h2><a href='listorders.php'>[return to orders listing]</a><a href="index.php">[Return to main page]</a></h2>

<h1>Pizza order for customer test</h1>
<form method="POST" id="selectPizza">
    
      <!-- date and time picker for the order -->
      <div>
        <label for="ordertime">Order Date and time</label>
        <input type="datetime-local" id="ordertime" name="ordertime" min="2022-04-01T00:00"  max="2023-04-01T00:00" required>
    </div>
        
        <p>
      <div>
        <!-- text input for extras -->
    <label for="extras">extras: </label>
    <input type="text" id="extras" name="extras" minlength="1" maxlength="200" required> 
    </div>
  </p> 

    
<hr>



<h2>Pizzas for this order</h2>

      <!-- select function for the order -->
      <div>
        Pizza <br><table id="add_more_pizza">
          <tr>
          <td>
            <label for="myInputs">1: </label>
          <select class="text-box" name="myInputs"  id= "myInputs" required>
          <option value="">please select pizza</option>
          <option value="1">Pizza 1</option>
          <option value="2">Pizza 2</option>
          <option value="3">Pizza 3</option>
          <option value="4">Pizza 4</option>
          <option value="5">Pizza 5</option>
          <option value="6">Pizza 6</option>
          <option value="7">Pizza 7</option>
          <option value="8">Pizza 8</option>
          <option value="9">Pizza 9</option>
          <option value="10">Pizza 10</option>
          </select>
          </td> 
    <!-- counter to select number of pizzas for the order -->
    <td>
      <input type="number" id="count" name="count" min="1" max="999" value="0" step="1" required>
    </td> 

    <!-- button to call the new orderline -->
        <td>
           <input type="button" name="add" value="add" onClick="addInput();"> 

        </td>
</tr>
        
    </table></br>
      </div>
    

  

</form>
  <hr>
    <input type="submit" name="submit" value="submit" form="selectPizza">
        <a href="index.php">[Cancel]</a> 



<!--</body>
</html> -->

<?php
//----------- page content ends here
include "footer.php";
?>

<!-- script to add a new order line when the "add" button is pressed  -->
<script>

      function addInput(){
       
        var newdiv = document.createElement('div');
        // creating the new order line as well a counter to indicate the number of pizzas needed.

        let formElement = 'Pizza <br><td><select class="pizza-selector" name="myInputs" id="myInputs">\
          <option value="1">Pizza 1</option>\
          <option value="2">Pizza 2</option>\
          <option value="3">Pizza 3</option>\
         <option value="4">Pizza 4</option>\
          <option value="5">Pizza 5</option>\
          <option value="6">Pizza 6</option>\
          <option value="7">Pizza 7</option>\
          <option value="8">Pizza 8</option>\
          <option value="9">Pizza 9</option>\
          <option value="10">Pizza 10</option>\
    </select>\
    </td>\
    <td><input class="pizza-quantity" type="number" id="count" name="count" min="0" max="999" value="0" step="1" required></td>\
    <td><input type="button" name="add" value="add" onClick="addInput();"></td>\
    <td><input type="button" value="remove" onClick="removeInput(this);"></td>'; // adds a button that removes the orderline
        
    //inserts the new orderline below the previous orderline.
    newdiv.insertAdjacentHTML('beforeend',formElement); 
        document.getElementById('add_more_pizza').appendChild(newdiv);

      }

    function removeInput(btn){
      btn.parentNode.remove();
    }

    </script>  