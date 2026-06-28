<?php 
// get url
include "inc.php";
include "logincheck.php";

$searching = '{
    "Type":"'.$_POST['Type'].'",
    "fromDate":"'.date('Y-m-d',strtotime($_POST['fromDate'])).'",
    "toDate":"'.date('Y-m-d',strtotime($_POST['toDate'])).'"
}';

 
  $url = $serverurlapi."General/gstReportAPI.php";
 
if($_POST['action']=="searchaction")
{
logger($InfoMessage." invoise status report API post list- ".$searching); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
//logger("RESPONSE RETURN FROM status report API: ". $result);
$resultData = json_decode($result, true);
curl_close($ch);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Invoice Status Report</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
    <?php include 'links.php'; ?>
    <script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
    </script>
    <!-- Favicon -->
</head>

<body>
    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav hk-nav-toggle">
        <!-- Top Navbar -->
        <?php include 'backofficeheader.php'; ?>
        <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
        <div class="hk-pg-wrapper"> 
            <section>
                <div class="container-fluid">
                    <form action="" method="POST" autocomplete="nope" id="exportfrm" />
                    <div class="row gy-bvc">
                        <div class="col-md-2">
                            <h6 style="font-weight: initial;">Selected Date</h6>

                            <select class="form-control" name="Type">
                                <option value="import" <?php if($_POST['Type']=="import"){ echo 'selected'; } ?>>Imported Date
                                </option>
                                <option value="document" <?php if($_POST['Type']=="document"){ echo 'selected'; } ?>>Document Date
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <h6 style="font-weight: initial;">From Date</h6>
                              <input type="text" name="fromDate" class="form-control datepicker"
                                  value="<?php echo $_POST["fromDate"]; ?>" readonly="readonly">
                        </div>
                        <div class="col-md-2">
                            
                                <h6 style="font-weight: initial;">To Date</h6>
                                <input type="text" name="toDate" class="form-control datepicker"
                                    value="<?php echo $_POST["toDate"]; ?>" readonly="readonly">
                            
                        </div>

                        <div class="col-md-3">
                            <div>
                                <h6>&nbsp;</h6>
                                <input type="hidden" id="action" name="action" value="" />
                                <input type="button" name="Search" class="btn btn-success"
                                    onClick="searchFunc('searchaction');" value="Search" />

                                <!-- <input type="button" name="Search" class="btn btn-success"
                                    onClick="searchFunc('exportaction');" value="Export Data" /> -->

                            </div>
                        </div>

                    </div>
                    </form>
                   

                </div>

                <script>
                function searchFunc(data) {
                    $('#action').val(data);
                    $('form#exportfrm').submit();
                }

                /*function searchFunc(data){
  $('#action').val(data);
  var branch = $('#Branch').val();
  if(branch == ''){
  $('#Branch').focus().css("border-color","#c00101");
  }else{
  $('form#exportfrm').submit();
 }
}*/

                $(document).ready(function() {
                    $('select').selectize({
                        sortField: 'text'
                    });
                });
                </script>
                <script>
                $(function() {
                    $(".datepicker").datepicker({
                        dateFormat: 'dd-mm-yy',
                        maxDate: 0
                    });
                });
                </script>
            </section>
            <div class="container-fluid">
                <table id="datatable" class="table table-striped table-bordered table-responsive" style="width:100%">
                    <thead>
                        <tr class="vcx-i hgt">
                            <th>FileImportDate</th>
                            <th>IRNno</th>
                            <th>QrCode</th>
                            <th>IRNDate</th>
                            <th>DocType</th>
                            <th>DocNum</th>
                            <th>DocDate</th>
                            <th>SupplierGSTIN</th>
                            <!-- <th>EinvStatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablesearch">
    <?php
    if(isset($resultData['GstData'])){                    
    $no=1;
    $crTotal = 0;
    $drTotal = 0;
    foreach($resultData['GstData'] as $rowdata){
       
        foreach($rowdata['OutputJson'] as $resultList){

            if($resultList['Irn']!=''){
    ?>
                        <tr class="uyt hgte">
                            <td><?php echo date('d-m-Y',strtotime($rowdata['ImportDate'])); ?></td>
                            <td><?php echo substr($resultList['Irn'],0,20); ?>...</td>
                            <td><?php echo substr($resultList['SignedQRCode'],0,20); ?>...</td>
                            <td><?php echo $resultList['IrnGenDt']; ?></td>
                            <td><?php echo $resultList['DocumentType']; ?></td>
                            <td><?php echo $resultList['DocumentNumber']; ?></td>
                            <td><?php echo $resultList['DocumentDate']; ?></td>
                            <td><?php echo $resultList['SupplierGSTIN']; ?></td>
                            <!-- <td><?php echo $resultList['EinvStatus']; ?></td> -->
                        </tr>
                        <?php
   } $no++; } }   ?>
                        
                        <?php }else{?>
                        <tr class="uyt hgte">
                            <td colspan="11">
                                <div align="center"><?php echo 'You Can Search...'; ?></div>
                            </td>
                        </tr>
                        <?php }
    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<style>
.ui-select {
    padding: 2%;
}

.hgt th {
    text-align: center;
    font-weight: bold;
}

.hgte td {
    text-align: center;
}

.gvre {
    display: flex;
    column-gap: 10px;
}

.lk-kl {
    width: fit-content;
    margin-left: auto;
    column-gap: 50px;
}

.pd-btn {
    padding: 3px 40px;
}

.pd-btn2 {
    padding: 3px 80px;
}

.flx {
    display: flex;
    column-gap: 12px;
}

.search-input-grid {
    display: grid;
    grid-gap: 5px;
}

.search-button {
    display: grid;
    grid-template-columns: auto auto;
    grid-gap: 20px;
}

.vcx-i {
    border-top: 2px solid;
    border-bottom: 2px solid;
}

.ht-jy {

    margin-top: 7%;
}

.inp-wuui {
    margin: 3px;
}

.gy-bvc {
    margin: 1%;
}

.nn-mb {
    margin-top: 3%;
}

.inp-w {
    width: 90%;
}

.uyt td {
    border: none;
}
</style>
<!--search filter-->
<script>
function searchingName() {
    var name = $("#bname").val().toLowerCase();
    $("#tablesearch tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(name) > -1)
    });
}
</script>
