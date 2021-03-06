<?php
	
  session_start();
  date_default_timezone_set('Asia/Jakarta');      
  include("doconnect.php");

  $user_check = $_SESSION['login_user'];
  include("../query/find_ledger.php");

	$arr = $_REQUEST['arr'];
	$arr1 = $_REQUEST['arr1'];	
	$quant = $_REQUEST['quant'];
	$invoice_id =  date("YmdHis");
	$today =  date('Y-m-d', strtotime($_REQUEST['invoice_date']));
	$time = date("H:i:s"); 
  $month = date("F");
  $created_date =  date("Y-m-d");
  $last_update_date =  date("Y-m-d");
  $type = 'Penjualan';
  $invoice_number = date("His");
  $description = $_REQUEST['description'];
  
  $outstanding_status = 'Open';
  $refund_status = 'No';
  $due_date = date('Y-m-d', strtotime($_REQUEST['due_date']));

if ( empty($_REQUEST['payment_method'])) {
    $payment_method = 'Cash';  
}
else{
    $payment_method = $_REQUEST['payment_method'];

}

if ( empty($_REQUEST['discount']) ) {
    $discount = 0;
  }
  else{
      $discount = $_REQUEST['discount'];
  }

  if (empty($_REQUEST['tax_code']) || $_REQUEST['tax_code'] ==='No' ) {
    $tax_code = 0;
  }
  else{
    $tax_code = 0.1;
  }

  $customer_name = $_REQUEST['customer_name'];
  
 
 
    $check_outlet = 
    "SELECT o.* 
    FROM employee e, outlet o 
    WHERE e.ledger_id = '".$ledger_new."' 
    and e.name = '".$user_check."' 
    and e.outlet_id = o.outlet_id and o.status = 'Active'";
    $result_outlet = mysqli_query($conn,$check_outlet);
    $existing_outlet = mysqli_fetch_assoc($result_outlet);

 

// buat print
    $seasons = array(); 
  
    for($x = 0; $x < count($quant); $x++ ){

      $check_item = "SELECT * FROM inventory WHERE ledger_id = '".$ledger_new."' and id = '".$arr[$x]."' ";
      $result_item = mysqli_query($conn,$check_item);
      $existing_item = mysqli_fetch_assoc($result_item);

    // if($quant[$x] > 0){                   
      $seasons[] = $existing_item['description'];
    // }
  }

  $subtotal = 0;

    for($x = 0; $x < count($arr); $x++ ){
      if($quant[$x] > 0){ 
      $subtotal += $arr1[$x] * $quant[$x];                  
      }
    }
  
  if ($discount === 0 && $tax_code === 0 ) {
    $discount_p = 0;
    $tax_p = 0;    
    $subtotal_p = sprintf("%15s",$subtotal);   
    $total_p = sprintf("%15s",$subtotal_p);
  }
  else if ($discount === 0 ) {
  $discount_p = 0;
  $subtotal_p = sprintf("%15s",$subtotal);
  $tax_p = sprintf("%15s",$tax_code * $subtotal);
  $total_p = sprintf("%15s",$subtotal_p + $tax_p);
  }
  else if ($tax_code === 0 ) {
  $discount_p = sprintf("%15s",$discount);
  $subtotal_p = sprintf("%15s",($subtotal - $discount));
  $tax_p = 0;
  $total_p = sprintf("%15s",$subtotal_p - $discount_p + $tax_p);
  }
  else{
  $discount_p = sprintf("%15s",$discount);
  $subtotal_p = sprintf("%15s",($subtotal - $discount));
  $tax_p = sprintf("%15s",($tax_code * ($subtotal - $discount)));
  $total_p = sprintf("%15s",$subtotal_p - $discount_p + $tax_p);
  }

    echo json_encode(array_values(array_filter($seasons))); echo "<br>";
    echo json_encode(array_values(array_filter($quant)));echo "<br>";
    echo json_encode(array_values(array_filter($arr1)));echo "<br>";
    echo json_encode(count($quant));echo "<br>";

    var_dump(array_values($seasons)); echo "<br>";
    var_dump(array_values($quant)); echo "<br>";
    var_dump(array_values($arr1)); echo "<br>";

?>

<html>
    <style type="text/css" media="print">
      @media print {
      
      @page { 
        margin: 0; 
        height: auto;
        }
      body { 
        margin: 1cm; 
        height: auto;
        }
    }
    </style>

        <!-- Bootstrap -->
    <link href="../../vendors/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../vendors/nprogress/nprogress.css" rel="stylesheet">
    
    <!-- Custom styling plus plugins -->
    <link href="../../build/css/custom.css" rel="stylesheet">


    <script type="text/javascript">

    function chr(x){
        return String.fromCharCode(x);
    }

    var ESC = chr(27);
    var LF = chr(10);
    var HT = chr(9);
    var VT = chr(11);
    // user friendly command name
    var PrnAlignLeft = ESC+'a'+chr(0);
    var PrnAlignCenter = ESC+'a'+chr(1);
    var PrnAlignRight = ESC+'a'+chr(2);
    var PrnItalic = ESC+chr(4);
    var PrnBoldOn = ESC+'G'+chr(1);
    var PrnBoldOff = ESC+'G'+chr(0);

    

    var subtotal = 0;
    var discount = <?php echo json_encode($discount)?>;
    var tax = <?php echo json_encode($tax_code)?>; 
    var count = <?php echo json_encode(count($quant))?>;
    var item_desc = <?php echo json_encode(array_values($seasons))?>;
    var quantity = <?php echo json_encode(array_values($quant))?>;
    var price = <?php echo json_encode(array_values($arr1))?>;

    var discount_p = <?php echo json_encode($discount_p)?>;
    var tax_p = <?php echo json_encode($tax_p)?>;
    var subtotal_p = <?php echo json_encode($subtotal_p)?>;
    var total_p = <?php echo json_encode($total_p)?>;

      function BtPrint(prn){
        var S = "#Intent;scheme=rawbt;";
        var P =  "package=ru.a402d.rawbtprinter;end;";
        var textEncoded = encodeURI(prn);
        window.location.href="intent:"+textEncoded+S+P;
      }

      function slip(){
        // собираем чек
        var prn = '';
        prn += PrnAlignCenter+<?php echo json_encode($existing_outlet['name']) ?>+LF;
        prn += PrnAlignCenter+<?php echo json_encode($existing_outlet['address']) ?>+LF;
        prn += PrnAlignCenter+<?php echo json_encode($existing_outlet['phone']) ?>+LF+LF
        prn += PrnBoldOn+'--------------------------------'+PrnBoldOff+LF;
        prn += PrnAlignLeft+'Date:'+<?php echo json_encode(sprintf("%27s",$time)) ?>+LF
        prn += PrnAlignLeft+'Receipt Number :'+<?php echo json_encode(sprintf("%16s",$invoice_number)) ?>+LF
        prn += PrnBoldOn+'--------------------------------'+PrnBoldOff+LF;

        for (var i = 0; i < count ; i++) {

         if(quantity[i] > 0){                   

        subtotal +=  parseInt((quantity[i]*price[i]));

          prn += PrnAlignLeft +item_desc[i]+LF;
          prn += PrnAlignLeft +quantity[i]+ 'x'+HT+quantity[i]*price[i]+LF;
          console.log(item_desc[i]);
          console.log(quantity[i]);
          console.log(price[i]);
          console.log(subtotal);
        }
      }

        prn += PrnAlignRight+PrnBoldOn+'--------------------------------'+PrnBoldOff+LF;
        
        console.log(discount);
        console.log(tax);
        console.log(<?php echo $subtotal_p; ?>);
        

        if (discount == 0 && tax == 0 ) {
        prn += PrnAlignRight+'Subtotal:'+subtotal_p+LF;
          }
        else if (discount == 0 ) {
        prn += PrnAlignRight+'Subtotal:'+subtotal_p+LF;
        prn += PrnAlignRight+'Tax:'+tax_p+LF;                  
          }
        else if (tax == 0 ) {
        prn += PrnAlignRight+'Discount:'+discount_p+LF;
        prn += PrnAlignRight+'Subtotal:'+subtotal_p+LF;        
          }
        else{
        prn += PrnAlignRight+'Discount:'+discount_p+LF;
        prn += PrnAlignRight+'Subtotal:'+subtotal_p+LF;        
        prn += PrnAlignRight+'Tax:'+tax_p+LF;
        }

        prn += PrnAlignRight+PrnBoldOn+'--------------------------------'+PrnBoldOff+LF;
        prn += PrnAlignRight+PrnBoldOn+'Total:'+(total_p)+PrnBoldOff+LF;
        prn += LF;
        BtPrint(prn);
    }

    </script>
    


<!-- <div id="printableArea">
  <div class="row">
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="x_panel">
        <div class="x_content">
          <div class="row">
            <div class="col-md-12 col-xs-12 col-lg-12">
              <p align="center"><?php echo $existing_outlet['name']; ?></p>
              <p align="center"><?php echo $existing_outlet['address']; echo ","; echo $existing_outlet['city']; echo "<br>"; echo $existing_outlet['province']; ?></p>
              <div class="row">
                <div class="col-md-6 col-xs-6">
                  <?php echo $today; ?><br>
                  Receipt Number<br>
                </div>
                <div class="col-md-6 col-xs-6 pull-right" style="text-align: right;">
                  <?php echo $time; ?><BR>
                  <?php echo $invoice_number; ?><BR>
                </div>
                <div class="clearfix"></div>
                <hr style="margin-top: 2px;">
                <div class="col-md-4 col-xs-4" style="text-align: left;">
                	
              	<?php
    							for($x = 0; $x < count($arr); $x++ ){

                    $check_item = "SELECT * FROM inventory WHERE ledger_id = '".$ledger_new."' and id = '".$arr[$x]."' ";
                    $result_item = mysqli_query($conn,$check_item);
                    $existing_item = mysqli_fetch_assoc($result_item);

    								if($quant[$x] > 0){							     	
                      echo $existing_item['description'];  echo'<br>';
                    }
            	   	}
                ?>

                </div>
                <div class="col-md-4 col-xs-4" style="text-align: center;">

                	<?php
    								for($x = 0; $x < count($arr); $x++ ){
    									if($quant[$x] > 0){							     	
                        echo $quant[$x]; echo "x"; echo '<br>'; 
                  		}
                    }

                ?>
                </div>
                <div class="col-md-4 col-xs-4 pull-right" style="text-align: right;">
                	
                	<?php
    								for($x = 0; $x < count($arr); $x++ ){
    									if($quant[$x] > 0){							     	
    							     echo "Rp."; echo number_format($arr1[$x] * $quant[$x]); echo '<br>'; 
                  		}
                    }
                  ?>

                </div>
                <div class="clearfix"></div>
                <hr style="margin-top: 2px;">
                <div class="col-md-6 col-xs-6">
                <p>
                  <?php              
                  
                  if ($discount === 0 && $tax_code === 0 ) {
                    echo "Subtotal"; echo "<br>";  
                  }
                  else if ($discount === 0 ) {
                  echo "Subtotal"; echo "<br>";
                  echo "Tax"; echo "<br>";                  
                  }
                  else if ($tax_code ===0 ) {
                  echo "Discount"; echo "<br>";
                  echo "Subtotal"; echo "<br>";                 
                  }
                  else{
                  echo "Discount"; echo "<br>";
                  echo "Subtotal"; echo "<br>";
                  echo "Tax"; echo "<br>";
                  }
                  ?>
                  
                  </p>
                </div>
                <div class="col-md-6 col-xs-6 pull-right" style="text-align: right;">

                <?php
              		$subtotal = 0;
    							for($x = 0; $x < count($arr); $x++ ){
    								if($quant[$x] > 0){	
    								$subtotal	+= $arr1[$x] * $quant[$x];						     	
    								}
                  }
                
                if ($discount === 0 && $tax_code === 0 ) {
                  echo "Rp."; echo number_format($subtotal - $discount); echo "<br>";  
                }
                else if ($discount === 0 ) {
                echo "Rp."; echo number_format($subtotal - $discount); echo "<br>";
                echo "Rp."; echo number_format($tax_code * ($subtotal - $discount)); echo "<br>";                  
                }
                else if ($tax_code ===0 ) {
                echo "Rp."; echo $discount; echo "<br>";
                echo "Rp."; echo number_format($subtotal - $discount); echo "<br>";                  # code...
                }
                else{
                echo "Rp."; echo $discount; echo "<br>";
                echo "Rp."; echo number_format($subtotal - $discount); echo "<br>";
                echo "Rp."; echo number_format($tax_code * ($subtotal - $discount)); echo "<br>";
                }
                ?>

                </div>
                <div class="clearfix"></div>
                <hr style="margin-top: 2px;">
                <div class="col-md-6 col-xs-6">
                <h4 style="margin-top: -10px;"><b>Grand Total</b></h4>
                </div>
                <div class="col-md-6 col-xs-6 pull-right" style="text-align: right;">
                <h4 style="margin-top: -10px;"><b>
                
                <?php
              		$subtotal = 0;
    							for($x = 0; $x < count($arr); $x++ ){
    								if($quant[$x] > 0){	
    								$subtotal	+= $arr1[$x] * $quant[$x];						     	
    								}
                  }
              
                  echo "Rp."; echo number_format(($subtotal - $discount) + ($tax_code * ($subtotal - $discount))) ; echo "<br>";
              
                ?>

                <button onclick="BtPrint(document.getElementById('printableArea').innerText)">Print text from &lt;pre&gt;...&lt;/pre&gt;</button>

                </b></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> -->

<button onclick="slip()">tes</button>

</html>






<script type="text/javascript">

      /*function printDiv(printableArea) {
           var printContents = document.getElementById(printableArea).innerHTML;
           var originalContents = document.body.innerHTML;

           document.body.innerHTML = printContents;

           print();
           setTimeout("closePrintView()", 1000);
           document.body.innerHTML = originalContents;
      }
      function closePrintView() {
        document.location.href = '../media_gallery.php';
    }
      document.getElementById("demo").innerHTML = printDiv('printableArea'); */
</script>

<?php


  if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_SESSION['rand'])){

  unset($_SESSION['rand']);
// HEADER
    // insert header invoice transaction
    $sql_header = "INSERT INTO invoice_header (invoice_id,invoice_number,invoice_date ,due_date,ledger_id,discount_amount,refund_status,outstanding_status , created_by,created_date,last_update_by,last_update_date,payment_method,customer_name,tax_code,outlet_id,description)
    VALUES ('".$invoice_id."','".$invoice_number."' , '".$today."' , '".$due_date."' , '".$ledger_new."', '".$discount."','".$refund_status."','".$outstanding_status."','".$user_check."','".$created_date."','".$user_check."','".$last_update_date."','".$payment_method."','".$customer_name."','".$tax_code."','".$outlet_new."','".$description."')";
    mysqli_query($conn, $sql_header);

// LINE
	for($y = 0; $y < count($arr); $y++ ){
		if ($quant[$y] > 0) {    

	 
      	$sql2 = "SELECT * from inventory WHERE id = '".$arr[$y]."'";
	    	$result = mysqli_query($conn, $sql2);

	    	while($row = $result->fetch_assoc()) {
          $cogs[$y] = $row["cogs"];

	    		if ($row["qty"] - $quant[$y] < 0 ){
		    			header("Location:../media_gallery.php");
	    		}
	    		else{

            // insert line invoice transaction
	    				$sql = "INSERT INTO invoice (inventory_item_id,unit_price,qty ,date,invoice_id,month,created_by , created_date,last_update_by,last_update_date,ledger_id,tax_code , tax_amount ,cogs)
						VALUES ('".$arr[$y]."','".$arr1[$y]."' , '".$quant[$y]."' , '".$today."' , '".$invoice_id."', '".$month."','".$user_check."','".$created_date."','".$user_check."','".$last_update_date."','".$ledger_new."','".$tax_code."','".$tax_code."' * '".$arr1[$y]."' * '".$quant[$y]."' , '".$cogs[$y]."' )";
						mysqli_query($conn, $sql);

            // insert mutasi 
              $sql = "INSERT INTO material_transaction (inventory_item_id, ledger_id,transaction_date,qty,description,created_by , created_date , last_update_by,last_update_date,type,outlet_id)
            VALUES ('".$arr[$y]."', '".$ledger_new."','".$created_date."',('".$quant[$y]."' * -1 ),NULL,'".$user_check."','".$created_date."','".$user_check."','".$created_date."','".$type."','".$outlet_new."')";
            mysqli_query($conn, $sql);
             
					// update stock
		    			$sql1 = "UPDATE inventory SET qty= qty - '".$quant[$y]."' , last_update_date= '".$last_update_date."' ,last_update_by= '".$user_check."' WHERE id = '".$arr[$y]."'";

						mysqli_query($conn, $sql1);
   
		    		}	
	    	}
		}
		
	}

    
    // insert payment kalau due datenya gak di centang.
    if ($due_date === $today) {

      $sql = "INSERT INTO ar_check_all (invoice_id, payment_number,payment_date,payment_type,payment_amount,created_by , created_date,last_update_by,last_update_date)
      VALUES ('".$invoice_id."', 'Dari Toko' , '".$today."' , '".$payment_method."' , (SELECT (sum(a.qty*a.unit_price) - '".$discount."' ) + (sum(tax_amount) - ('".$discount."' * '".$tax_code."') ) FROM invoice a where a.invoice_id = '".$invoice_id."' and a.ledger_id = '".$ledger_new."'  ) ,'".$user_check."','".$created_date."','".$user_check."','".$last_update_date."')";
      mysqli_query($conn, $sql);

      // update amount_due_original
      $sql_header = "UPDATE invoice_header set amount_due_remaining =  0 , outstanding_status = 'Paid' where invoice_id = '".$invoice_id."'";
      mysqli_query($conn, $sql_header);

    }else{

    // update amount_due_remaining
    $sql_header = "UPDATE invoice_header ih set ih.amount_due_remaining = (select (sum(unit_price*qty) - '".$discount."') + (sum(tax_amount) - ('".$discount."' * '".$tax_code."')) from invoice i where i.invoice_id = ih.invoice_id) where ih.invoice_id = '".$invoice_id."'";
    mysqli_query($conn, $sql_header);
    
    }

	mysqli_close($conn);

}
?>
