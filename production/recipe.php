<?php
include("controller/doconnect.php");
session_start();
include("controller/session.php");
include("query/find_ledger.php");
include("query/redirect_billing.php");
?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
    <title>Bonne Journée! </title>

        <!-- Toastr -->
    <link rel="stylesheet" href="../vendors/toastr/toastr.min.css">
    <script src="../vendors/toastr/jquery-1.9.1.min.js"></script>
    <script src="../vendors/toastr/toastr.min.js"></script>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome-2/css/all.css" rel="stylesheet"> 
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- jQuery custom content scroller -->
    <link href="../vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet"/>

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        
        <!-- Sidebar Menu -->
        <?php
          if ($_SESSION['userRole'] == "Staff"){
            session_destroy(); 

            session_start();
            $logout = true;
            $_SESSION['logout'] = $logout;
            
            header("location: login.php"); 
          } else if ($_SESSION['userRole'] == "Admin") {
            include("view/sidebar.php");
          }
        ?>
        <!-- End Of Sidebar  -->
        
        <!-- Top Navigation -->
        <?php include("view/top_navigation.php"); ?>
        <!-- End Of Top Navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>RECIPE</h3>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detail</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="formregisterrecipe" class="form-horizontal form-label-left input_mask" method="POST" action="controller/doaddrecipe.php">
                      <div class="form-group">
                        <label class="col-md-1 col-sm-3 col-xs-3">Recipe Name</label>
                        <div class="col-md-3 col-sm-3 col-xs-12">
                          <select class="form-control" id="recipe_name" name="recipe_name[]" required="required">
                          <?php

                            $sql = "SELECT description , id 
                            FROM inventory 
                            where ledger_id = '".$ledger_new."'";
                            $result = $conn->query($sql);
                            $a = 0;
                            while($row = $result->fetch_assoc()) {
                          ?>
                          <option value="<?php echo $row["id"] ?>"> <?php echo $row["description"] ?></option>
                          <?php
                            }
                          ?>
                          </select>
                        </div>
                      </div>
                      <div class="clearfix"><br></div>
                    <div class="row">
                      <div class="col-md-8 col-sm-12 col-xs-12">
                        <div class="x_panel">
                          <div class="x_title">
                            <h2><i class="fa fa-align-left"></i> Ingredient </h2>
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">

                            <!-- Ingredient LINE  -->
                            <div class="panel-body">
                              <div class="panel panel-default" style="padding-top: 20px;  border: 0px;">

                                                  
                              <div class="table-responsive" >
                                <table class="table" id="myTable">
                                  <tr>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th>Quantity</th>
                                    <th></th>
                                  </tr>
                                  <tr>
                                    <td><input type="hidden" name="counter[]" id="counter">#</td>
                                    <td>
                                      <select class="form-control item name" id="inventory_item_id" name="inventory_item_id[]" required="required">
                                      <?php

                                        $sql = "SELECT description , id 
                                        FROM inventory 
                                        where ledger_id = '".$ledger_new."'";
                                        $result = $conn->query($sql);
                                        $a = 0;
                                        while($row = $result->fetch_assoc()) {
                                      ?>
                                      <option value="<?php echo $row["id"] ?>"> <?php echo $row["description"] ?></option>
                                      <?php
                                        }
                                      ?>
                                      </select>
                                    </td>
                                    <td><input type="number" class="form-control qty" id="qty" name="qty[]" required="required"></td>
                                    <td></td>
                                  </tr>
                                </table>
                              </div>


                              <button class="btn btn-success" type="button" onclick="myCreateFunction();" style="margin-top: 20px;"> <b>Insert New Row</b>
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 
                              </button>

                              <div class="clear"></div>    
                              </div>
                            </div>
                            <!-- END OF PO LINE -->
                          </div>
                        </div>
                      </div>
                    </div>
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12" align="center">
						              <button class="btn btn-primary" type="reset">Reset</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <?php include("view/footer.php"); ?>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.js"></script>
    <!-- FastClick -->
    <script src="../vendors/fastclick/lib/fastclick.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="../build/js/custom.min.js"></script>

    <!-- jQuery custom content scroller -->
    <script src="../vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>

    <script src="../production/common/error.js"></script>
    
    <script type="text/javascript">

function myDeleteFunction() {
    document.getElementById("myTable").deleteRow(1);
}

function deleteRow(row) {
  var i = row.parentNode.parentNode.rowIndex;
  document.getElementById('myTable').deleteRow(i);
}



function myCreateFunction() {
    var table = document.getElementById("myTable");
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    cell1.innerHTML = '<td><input type="hidden" name="counter[]" id="counter">#</td>';
    cell2.innerHTML = '<select class="form-control itemname" id="inventory_item_id" name="inventory_item_id[]" required="required"><?php $sql = "SELECT description,id FROM inventory";$result = $conn->query($sql);$a = 0;while($row = $result->fetch_assoc()) {?><option value="<?php echo $row["id"] ?>"> <?php echo $row["description"] ?></option><?php } ?></select>';
    cell3.innerHTML = '<td><input type="text" class="form-control qty" id="qty" name="qty[]" required="required"></td>';
    cell4.innerHTML = '<td><button class="btn btn-danger" type="button" onclick="deleteRow(this);"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></td>';
}

                                                       

    </script>
	
  </body>
</html>


