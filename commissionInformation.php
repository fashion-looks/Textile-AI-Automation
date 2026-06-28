<?php 
// get url 
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;

$loginType = strtoupper($_SESSION['Type']);

if($loginType=="BRANCH"){
	$branchCode = $_SESSION["BID"];
}else{
	$branchCode = '';
}



// if($_GET['did']!=""){
// $jsondelete = '{
// 	"editId":"'.decode($_GET['did']).'",
// 	"type":"'.decode($_GET['type']).'"
// }';

// $branchCode = trim($_GET['branchCode']);
// $voucherNo = trim($_GET['voucherNo']);
// $productType = trim($_GET['productType']);
  
// $url = $serverurlapi."General/editDeleteRechargeAPI.php";
// $resultData = postCurlData($url,$jsondelete);
// logger('Response return from listBranchRechargeAPI: '.$resultData);
// }


// if($_GET['action']=="searchaction"){

// $branchCode = trim($_GET['branchCode']);
// $voucherNo = trim($_GET['voucherNo']);
// $productType = trim($_GET['productType']);
  
// $jsonData = '{
//   	"branchCode":"'.$branchCode.'",
//     "voucherNo":"'.$voucherNo.'",
// 	"productType":"'.$productType.'"
// }';

$url = $serverurlapi."General/commissionInformationAPI.php";
$resultData = postCurlData($url,$jsonData);
logger('Response return from commissionInformationAPI: '.$resultData);
$commissionData = json_decode($resultData);

// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Commission Information</title>
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
  	<div class="hk-pg-wrapper"  style="">
  		<div class="container-fluid">
	  
	   <div class="row gy-bvc nn-mb">
        <div class="col-md-12">
          <div class="row lk-kl">
            <div class=""> <a href="addCommissionInformation.php" class="btn btn-default browsebutton pd-btns">Add Commission Period</a></div>
          </div>
        </div>
      </div>
    </div>
    
		<div class="container-fluid" style="margin-top:20px">
		<div id="tabledata" style="padding: 10px;"> 
	<?php  if(isset($_SESSION['error'])!=''){ ?>
	<div class="bs-example" id="messageDiv">
	<!-- Success Alert -->
	<div class="alert alert-dismissible fade show" style="border: solid 2px; border-block-color: green; font-weight: 800; font-size: 17px; color: green;">
		  <?php echo $_SESSION['error'];unset($_SESSION['error']); ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
	</div>
	<?php } ?>
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
        <tr class="vcx-i hgt">
        <th>S&nbsp;No</th>
			  <th>From Date</th>
			  <th>To Date</th>
        <th>Commission Date</th>
        <th>Due Date</th>
			  </tr>
          </thead>
          <tbody id="tablesearch">
            <?php
    if(isset($commissionData->status)=='true'){
    if(isset($commissionData->commissionPeriod)){                    
    $no=1;
    foreach($commissionData->commissionPeriod as $resultList){
    ?>
            <tr class="uyt hgte">
        <td><?php echo $no; ?></td>
			  <td><?php echo $resultList->FromDate; ?></td>
			  <td><?php echo $resultList->ToDate; ?></td>
        <td><?php echo $resultList->CommissionDate; ?></td>
        <td><?php echo $resultList->DueDate; ?></td>
      </tr>
			  <?php $no++; } } }
  else{?>
    <tr class="uyt hgte">
<td colspan="14"><div align="center"><?php echo 'You Can Search...'; ?></div></td>    
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
  table.dataTable>thead>tr>th:not(.sorting_disabled), table.dataTable>thead>tr>td:not(.sorting_disabled),table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td {
font-size: 12px!important;
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