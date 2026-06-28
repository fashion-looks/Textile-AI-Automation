<?php
// get url
include "inc.php";
include "logincheck.php";

$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;
logger($InfoMessage." URL for API - ".$url);
$branchCode = '';

$branchCode = $_POST['Branch'];
$searching = '{
    "Type":"'.$_POST['Type'].'",
    "Branch":"'.$_POST['Branch'].'",
	"fromDate":"'.date('Y-m-d',strtotime($_POST['fromDate'])).'",
	"toDate":"'.date('Y-m-d',strtotime($_POST['toDate'])).'"
}';
$url = $serverurlapi."vouchers/ledgerAPI.php";

if($_POST['action']=="searchaction")
{


logger($InfoMessage." URL for API post - ".$url);
logger($InfoMessage." Value for API post list- ".$searching);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
//logger("RESPONSE RETURN FROM ACCOUNT STATEMENT: ". $result);
$ledgerData = json_decode($result, true);
curl_close($ch);
}

if($_POST['action']=="exportaction"){

  logger($InfoMessage." export URL for API - ".$url);
  logger($InfoMessage." export Value for API - ".$searching);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  $result = curl_exec($ch);
  //logger("RESPONSE RETURN FROM ACCOUNT EXPORT STATEMENT: ". $result);
  $ledgerData = json_decode($result, true);
  curl_close($ch);
  // Filter the excel data
  function filterData(&$str){
      $str = preg_replace("/\t/", "\\t", $str);
      $str = preg_replace("/\r?\n/", "\\n", $str);
      if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // Excel file name for download
  $fileName = "Statement_" . $_POST['fromDate'] . "_".$_POST['toDate'].".xls";

  // Column names
  $fields = array('S.No','Voucher No', 'Voucher Date', 'Narration', 'Bill No', 'Debit', 'Credit', 'Balance');

  // Display column names as first row
  $excelData = implode("\t", array_values($fields)) . "\n";

   if(isset($ledgerData['LedgerList'])){
    $no=1;
    foreach($ledgerData['LedgerList'] as $resultList){

      if($resultList['Type'] == 'digitization'){
        $tp =  'Digitization for Ack No- '.$resultList['AckNo'];
      }
      elseif($resultList['Type'] == 'recharge'){
        $tp =  'Wallet Recharge';
      }
      elseif($resultList['Type'] == 'acceptance'){
        $tp =  'Acceptance for Ack No- '.$resultList['AckNo'];
      }
      else{
        $tp = $resultList['Detail'];
      }


         $lineData = array($no,$resultList['VoucherNo'],date("d-m-Y",strtotime($resultList['Date'])), $tp, $resultList['BillNo'], $resultList['Debit'], $resultList['Credit'], $resultList['Balance']);
          array_walk($lineData, 'filterData');
          $excelData .= implode("\t", array_values($lineData)) . "\n";

      $no++;
      }
    }

    // Headers for download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    // Render excel data
    echo $excelData;


  exit;
  }

$loginType = strtoupper($_SESSION['Type']);
if($loginType=="BRANCH"){
	$branchCodeNum = $_SESSION["BID"];
}else{
	$branchCodeNum = $branchCode;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Ledger</title>
<meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
<?php include 'links.php'; ?>
<script>
$(document).ready(function(){
    $('#datatable').DataTable();
} );
</script>
<!-- Favicon -->
</head>
<body>
<!-- HK Wrapper -->
<div class="hk-wrapper hk-vertical-nav hk-nav-toggle">
  <!-- Top Navbar -->
  <?php include 'backofficeheader.php'; ?>
  <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  <div class="hk-pg-wrapper"  style="background-image: url(../html/dist/img/Religare-Dashboard-BG.JPG);">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <section>
    <div class="container-fluid">
    <form action="" method="POST" autocomplete="nope" id="exportfrm" />
        <div class="row gy-bvc">

       <!--  <div class="col-md-2" style="display:none">
          <div >
          <h6 style="font-weight: initial;">Type</h6>
           <select name="Type" class="form-control">
            <option value="">Select</option>
             <option value="Branch" <?php // if($_POST["Type"]=="Branch"){ echo "selected"; } ?>>Branch</option>
             <option value="Religare" <?php // if($_POST["Type"]=="Religare"){ echo "selected"; } ?>>Religare</option>
             <option value="NSDL" <?php // if($_POST["Type"]=="NSDL"){ echo "selected"; } ?>>NSDL</option>
           </select>
          </div>
        </div> -->
        <input type="hidden" name="Type" value="Religare" >
        <div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">Branch Account</h6>
           <!--<input type="text" name="Branch" id="Branch" class="form-control" maxlength="5" value="<?php echo $_POST["Branch"]; ?>">-->
		   <select class="form-control" name="Branch">
			   			<option value="">Select</option>
			   			<?php
			   			$jsonData = '{
					"AccountName":"",
					"GroupId":"",
					"Status":"1"
				}';
				$newurl = $serverurlapi."masters/accountNameAPI.php";
				$resultData = postCurlData($newurl,$jsonData);
				//logger('Response return from account Name API: '.$resultData);
				$accountData = json_decode($resultData);
				if(isset($accountData->status)=='true'){
				if(isset($accountData->AccountNameData)){
				foreach($accountData->AccountNameData as $resultList){
					?>
			   	<option value="<?php echo $resultList->Id; ?>" <?php if($resultList->Id==trim($_POST["Branch"])){ echo 'selected'; }?>><?php echo $resultList->AccountName; ?></option>
			   	<?php } } }	?>
			   	</select>
          </div>
        </div>
		<div class="col-md-2" style="display: none;">
          <div >
          <h6 style="font-weight: initial;">Voucher No</h6>
           <input type="text" name="voucherNo" class="form-control"  value="<?php echo $_POST["voucherNo"]; ?>" >
          </div>
        </div>
		<div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">From Date</h6>
           <input type="text" name="fromDate" class="form-control datepicker"  value="<?php echo $_POST["fromDate"]; ?>" readonly="readonly">
          </div>
        </div>
		<div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">To Date</h6>
           <input type="text" name="toDate" class="form-control datepicker"  value="<?php echo $_POST["toDate"]; ?>" readonly="readonly">
          </div>
        </div>

        <div class="col-md-3">
          <div>
            <h6>&nbsp;</h6>
          <input type="hidden" id="action" name="action" value="" />
          <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('searchaction');" value="Search" />

				  <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('exportaction');" value="Export Data" />

          </div>
        </div>

        </div>
      </form>
	 <!-- <div class="row gy-bvc">  -->
	  <?php //	  foreach($balanceRes->DataList as $productBalance){ ?>
      <!-- <div class="col-md-2">
            <div style="border: 1px solid #ccc; padding: 10px;">
              <h6 style="font-weight: initial;color: #22af47;"><?php // echo $productBalance->ProductName; ?> Balance</h6>
              <label style="font-weight: 700;">INR <?php // echo $productBalance->Balance; ?></label>
            </div>
      </div> -->
      <?php //  }  ?>

	 <!-- </div> -->

    </div>

     <script>
      function searchFunc(data){
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

    $(document).ready(function () {
      $('select').selectize({
          sortField: 'text'
      });
  });
 </script>
 <script>
$( function() {
  $( ".datepicker" ).datepicker({
    dateFormat: 'dd-mm-yy',
    maxDate: 0
  });
});

</script>
      </section>
      <div class="container-fluid">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
              <th>S.No</th>
              <th>Voucher No</th>
              <th>Voucher Date</th>
              <th>Narration</th>
              <th>Bill No</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody id="tablesearch">
	<?php
    if(isset($ledgerData['LedgerList'])){
    $no=1;
    $crTotal = 0;
    $drTotal = 0;
    foreach($ledgerData['LedgerList'] as $resultList){
    ?>
            <tr class="uyt hgte">
              <td><?php echo $no; ?></td>
			  <td><?php echo $resultList['VoucherNo'] ?></td>
        <td><?php echo date("d-m-Y",strtotime($resultList['Date'])); ?></td>
              <td><?php
              if($resultList['Type'] == 'digitization'){
                echo 'Digitization for Ack No- '.$resultList['AckNo'];
              }
              elseif($resultList['Type'] == 'recharge'){
                echo 'Wallet Recharge';
              }
              elseif($resultList['Type'] == 'acceptance'){
              echo 'Acceptance for Ack No- '.$resultList['AckNo'];
              }
              else{
                echo $resultList['Detail'];
              }

               ?>
			  </td>
        <td>
          <?php if($resultList['BillNo']!="" && $resultList['Type']=="bill"){ ?>
        <a href="obligationReport.php?bid=<?php echo encode($branchCodeNum); ?>&bpi=<?php echo encode($resultList['BillPeriodId']); ?>" target="_blank"><?php echo $resultList['BillNo']; ?></a>
        <?php }

         if($resultList['BillNo']!="" && $resultList['Type']=="commission"){ ?>
        <a href="commissionDetail.php?cid=<?php echo encode($branchCodeNum); ?>&bpi=<?php echo encode($resultList['BillPeriodId']); ?>" target="_blank"><?php echo $resultList['BillNo']; ?></a>
        <?php } ?>

      </td>
              <td><?php echo $resultList['Debit']; $drTotal += $resultList['Debit']; ?></td>
              <td><?php echo $resultList['Credit']; $crTotal += $resultList['Credit']; ?></td>
              <td><?php echo $resultList['Balance']; ?></td>
            </tr>
            <?php
    $no++;} ?>
      <tr style="background:#d3d2d2; text-align:center;">
        <td colspan="4"></td>
          <th><b>Total</b></th>
          <th><b><?php echo $drTotal; ?></b></th>
          <th><b><?php echo $crTotal; ?></b></th>
          <td></td>
      </tr>

  <?php }else{?>
    <tr class="uyt hgte">
<td colspan="8"><div align="center"><?php echo 'You Can Search...'; ?></div></td>
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
  .ui-select{
    padding: 2%;
  }
  .hgt th{
    text-align: center;
    font-weight: bold;
  }
  .hgte td{
    text-align: center;
  }
  .gvre{
        display: flex;
    column-gap: 10px;
  }
  .lk-kl{
  width: fit-content;
    margin-left: auto;
    column-gap: 50px;
  }
  .pd-btn{
    padding: 3px 40px;
  }
  .pd-btn2{
    padding: 3px 80px;
  }
  .flx{
  display: flex;
  column-gap: 12px;
  }
  .search-input-grid{
     display: grid;
    grid-gap: 5px;
  }
  .search-button{
      display: grid;
    grid-template-columns: auto auto;
    grid-gap: 20px;
  }
  .vcx-i{
    border-top: 2px solid;
    border-bottom: 2px solid;
  }
  .ht-jy{

    margin-top:7%;
  }
.inp-wuui{
  margin: 3px;
}
.gy-bvc{
  margin: 1%;
}
.nn-mb{
  margin-top: 3%;
}
.inp-w{
  width: 90%;
}
.uyt td{
  border: none;
}
</style>
<!--search filter-->
<script>
function searchingName(){
    var name = $("#bname").val().toLowerCase();
    $("#tablesearch tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(name) > -1)
    });
}
</script>
