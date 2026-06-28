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

if($_POST['action']=='approveall'){
 	$ackJson = '';
	foreach($_POST['acknowledgmentchecksingle'] as $voucherNumber){
		$ackJson.= '{
				"voucherNumber":"'.$voucherNumber.'"
			},';
	}

	$jsonPost = '{
		"userId": "'.$_SESSION['UID'].'",
    "ip":"'.$_SERVER["REMOTE_ADDR"].'",
		"ListOfVoucher":['.rtrim($ackJson,',').']
	}';

	$url =  $serverurlapi."vouchers/updateRechargeAPI.php";
	$response = postCurlData($url,$jsonPost);
	logger("RESPONCE RETURN from Recharge approve API: ". $response);
	$responseData = json_decode($response);
	$Message = $responseData->Message;
	$Status = $responseData->Status;
	if($Status == 0){
		$_SESSION['error'] = 'Record Approved Successfully.';
	}else{
		$_SESSION['error'] = 'Error in Approve.';
	}


}


if($_GET['did']!=""){
$jsondelete = '{
	"editId":"'.decode($_GET['did']).'",
	"type":"'.decode($_GET['type']).'"
}';

$branchCode = trim($_GET['branchCode']);
$voucherNo = trim($_GET['voucherNo']);
// $productType = trim($_GET['productType']);

$url = $serverurlapi."General/editDeleteRechargeAPI.php";
$resultData = postCurlData($url,$jsondelete);
//logger('Response return from listBranchRechargeAPI: '.$resultData);
}


if($_POST['action']=="searchaction"){

$branchCode = trim($_POST['branchCode']);
$voucherNo = trim($_POST['voucherNo']);
$fromDate = date('Y-m-d',strtotime($_POST['fromDate']));
$toDate = date('Y-m-d',strtotime($_POST['toDate']));
// $productType = trim($_GET['productType']);

$jsonData = '{
  	"branchCode":"'.$branchCode.'",
    "voucherNo":"'.$voucherNo.'",
    "fromDate":"'.$fromDate.'",
    "toDate":"'.$toDate.'"
}';

$url = $serverurlapi."vouchers/listBranchRechargeAPI.php";
$resultData = postCurlData($url,$jsonData);
logger('Response return from listBranchRechargeAPI: '.$resultData);
$accountData = json_decode($resultData);

}
/*else{


$jsonData = '{
		"branchCode":"'.$branchCode.'"
}';
$url = $serverurlapi."General/listBranchRechargeAPI.php";
$resultData = postCurlData($url,$jsonData);
logger('Response return from listBranchRechargeAPI: '.$resultData);

$accountData = json_decode($resultData);

}*/





?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>List Bank Voucher</title>
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

  <?php
if(strtoupper($_SESSION['Type']) == 'BRANCH'){
include 'header.php';
}else{
   include 'backofficeheader.php';
}
    ?>
  	<div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  	<div class="hk-pg-wrapper"  style="">
  		<div class="container-fluid">
      <form action="" method="POST" autocomplete="nope" id="exportfrm" />

        <div class="row gy-bvc">
		<!-- <div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">Product Type</h6>
           <select class="form-control" name="productType" id="productType">
              <option value="">Select</option>
              <option value="PAN" <?php // if($_GET['productType']=="PAN"){ echo "selected"; }?>>PAN</option>
              <option value="TAN" <?php // if($_GET['productType']=="TAN"){ echo "selected"; }?>>TAN</option>
            </select>
          </div>
        </div> -->
		<?php if(strtoupper($_SESSION['Type'])!='BRANCH'){ ?>
        <div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">Branch Code</h6>
           <input type="text" name="branchCode" class="form-control " value="<?php echo $_GET['branchCode']; ?>"  autocomplete="off" />
          </div>
        </div>
		<?php }else{ ?>
		<input type="hidden" name="branchCode" value="<?php echo $branchCode; ?>" >
		<?php }?>
        <div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">Voucher No</h6>
           <input type="text" name="voucherNo" class="form-control " value="<?php echo $_GET['voucherNo']; ?>"  autocomplete="off" />
          </div>
        </div>
        <div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">From Date</h6>
           <input type="text" name="fromDate" class="form-control datepicker" value="<?php echo $_GET['fromDate']; ?>"  readonly />
          </div>
        </div>
		<div class="col-md-2">
          <div >
          <h6 style="font-weight: initial;">To Date</h6>
           <input type="text" name="toDate" class="form-control datepicker" value="<?php echo $_GET['toDate']; ?>"  readonly />
          </div>
        </div>
        <div class="col-md-2">
          <div>
            <h6>&nbsp;</h6>
          <input type="hidden" id="action" name="action" value="searchaction" />
          <input type="submit" name="Search" class="btn btn-success" value="Search" />
          </div>
        </div>
		</div>
      </form>


	  <div class="row gy-bvc nn-mb">
        <div class="col-md-12">
          <div class="row lk-kl">
            <div class=""> <a href="addBankVoucherEntry.php" class="btn btn-default browsebutton pd-btns">Add Bank Voucher</a></div>
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
        <form method="post" id="appfrom">
          <div id="approvebutton" style="display:none; float:left;">
            <button type="button" class="btn btn-success" style="font-size: 13px; font-weight: 700;" onClick="processBar();confirmsubmit();" >Approve</button>
          </div>
          <input type="hidden" name="action" value="approveall">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
			<?php if($loginType=="BACKHO"){ ?><th><input  name="ackknowledmentCheckAll" type="checkbox" class="" id="ackknowledmentCheckAll" style="height: 17px;width: 50px;margin-top: 0;text-align: center;" /></th> <?php } ?>
              <th>Voucher&nbsp;No</th>
			  <!-- <th>Product Type</th> -->
               <?php if(strtoupper($_SESSION['Type'])!='BRANCH'){ ?> <th>Branch A/c</th><?php } ?>
              <th>Voucher&nbsp;Date</th>
              <!--<th>Payment Type</th>-->
			  <th>Amount</th>
			  <th>Cheque No.</th>
			  <th>Check&nbsp;Date</th>
			  <th>Bank Name</th>
        <th>Religare Bank Name</th>
			  <th>Added&nbsp;By</th>
			  <th>Document</th>
			  <th>Narration</th>
			  <?php if($loginType=="BRANCH"){ ?>
			  <th>Status</th>
			 <th>Action</th>
			 <?php } ?>
            </tr>
          </thead>
          <tbody id="tablesearch">
            <?php
    if(isset($accountData->status)=='true'){
    if(isset($accountData->WalletData)){
    $no=1;
    foreach($accountData->WalletData as $resultList){

    ?>
            <tr class="uyt hgte">
			 <?php if($loginType=="BACKHO"){ ?><td><input type="checkbox" style=" opacity:1; cursor: pointer; height: 17px; width: 50px; margin-top: 3px;" value="<?php echo $resultList->VoucherNo; ?>" name="acknowledgmentchecksingle[]"  class="deleteack"/></td><?php } ?>
              <td><?php echo $resultList->VoucherNo; ?></td>
			  <!-- <td><?php // echo $resultList->ProductType; ?></td> -->
              <?php if(strtoupper($_SESSION['Type'])!='BRANCH'){ ?><td><?php echo $resultList->BranchCode; ?></td><?php } ?>
			  <td><?php echo dateFormatAll($resultList->VoucherDate); ?></td>
			  <!--<td><?php // echo '-'; ?></td>-->
			  <td><?php echo $resultList->Credit; ?></td>
			  <td><?php echo $resultList->Cheque; ?></td>
			  <td><?php echo dateFormatAll($resultList->ChequeDate); ?></td>
			  <td><?php echo $resultList->BankName; ?></td>
			  <td><?php echo $resultList->ReligareBankName; ?></td>
			 <td title="<?php echo $resultList->AddedBy.' '.date("d-M-Y h:ia",strtotime($resultList->AddedDate)); ?>"><?php if($resultList->AddedBy!="Not Found"){ echo substr($resultList->AddedBy,0,5).'..'; } ?></td>
			 <td><strong><a href="uploads/<?php echo $resultList->Attachment; ?>" target="_blank">View</a></strong></td>
             <!--<td>
				<div id="hide<?php echo $resultList->VoucherNo; ?>" onClick="funccheck(<?php echo $no; ?>,'<?php echo $resultList->VoucherNo; ?>');" style="cursor: pointer;color: white;background: #2f9e41;border: 1px solid #2f9e41;border-radius: 4px;padding: 3px 0px; <?php  if($resultList->WalletFlag == 0){ ?>display: block; <?php }else{ ?>display: none;<?php } ?>">Approve</div>

                <div id="show<?php echo $resultList->VoucherNo; ?>" style="color: white;font-size: 13px;background: #1f7140;width: fit-content;padding: 1px 5px;border-radius: 50%;display: block;margin: auto; <?php  if($resultList->WalletFlag == 0){ ?>display: none; <?php }else{ ?>display: block;<?php } ?>"><i class="fa fa-check"></i></div>

              </td>-->
			  <td><a  style="color:#0000FF; " href="#" data-toggle="modal" data-target="#modalpop" onClick="opmodalpop('Narration','modelpop.php?action=showNarration&ndetail=<?php echo htmlentities($resultList->Narration); ?>','100%','auto');"><?php echo substr($resultList->Narration,0,10); ?>...</a></td>
			   <?php if($loginType=="BRANCH"){ ?>
			  <td>Pending</td>

			  <td>
			  <a href="addBankVoucherEntry.php"><i class="fa fa-pencil" style="font-size: 18px; color:#0000FF;"></i></a>
			  <a href="listBankVoucherEntry.php?type=<?php echo encode("delete"); ?>&did=<?php echo encode($resultList->Id); ?>&action=searchaction&branchCode=<?php echo $_GET['branchCode']; ?>&productType=<?php echo $_GET['productType']; ?>" onClick="return  confirm('are you sure you want to delete?');"><i class="fa fa-trash" style="font-size: 18px; color:#FF0000;"></i></a>
			  </td>
			  <?php } ?>
            </tr>
            <?php
    $no++;
  }
}
  }
  else{?>
    <tr class="uyt hgte">
<td colspan="14"><div align="center"><?php echo 'You Can Search...'; ?></div></td>
    </tr>
    <?php }
    ?>
          </tbody>
        </table>
		</form>
		</div>
      	</div>
    </div>
  </div>
  <script>
  function confirmsubmit(){
  	var confirmfrom = confirm("Are you sure you want to approve?");
	if(confirmfrom==true){
		$('#appfrom').submit();
	}else{
		$('#processBar').hide();
	}
  }
  </script>
  <script type="text/javascript">
    $(document).ready(function(){
    // check uncheck all inclusions
    $("#ackknowledmentCheckAll").click(function(){
    if(this.checked){
      $('.deleteack').each(function(){
        this.checked = true;
      })
    }else{
      $('.deleteack').each(function(){
        this.checked = false;
      })
    }
    });

    });

    window.setInterval(function(){
      checked = $("#tabledata input[type=checkbox]:checked").length;
      if(!checked) {
    $("#approvebutton").hide();
      } else {

    $("#approvebutton").show();
    }
}, 100);
</script>
<?php include 'footer.php'; ?>
</body>
</html>
<script>
$( function() {
  $( ".datepicker" ).datepicker({
    dateFormat: 'dd-mm-yy',
    maxDate: 0
  });
});


function funccheck(no,voucher){
	var voucher = encodeURI(voucher);
	var confirmed = confirm("Do you want to Approve Voucher no "+voucher);
	if(confirmed){
		$('#hide'+voucher).load('frmaction.php?act=walletApprove&voucher='+voucher);

	}

}
</script>
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
