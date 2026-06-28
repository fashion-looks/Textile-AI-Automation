<?php
// get url
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;

$loginType = strtoupper($_SESSION['Type']);

if($_GET['action']=="searchaction"){

$VoucherNo = trim($_GET['VoucherNo']);
$TransactionDate = trim($_GET['TransactionDate']);
$Type = "BP";

$jsonData = '{
  	"VoucherNo":"'.$VoucherNo.'",
	"TransactionDate":"'.$TransactionDate.'",
  "Type":"'.$Type.'"
}';

$newurl = $serverurlapi."vouchers/listVoucherEntryAPI.php";
$resultData = postCurlData($newurl,$jsonData);
logger('Response return from listVoucherEntryAPI: '.$resultData);
$accountData = json_decode($resultData);

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Bank Payment</title>
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
      <form action="" method="GET" autocomplete="nope" id="exportfrm"  />
      <div class="row gy-bvc">
        <div class="col-md-4">
          <div >
            <h6 style="font-weight: initial;">Voucher No</h6>
            <input type="text" name="VoucherNo" class="inp-w ui-select" value="<?php echo $_GET['VoucherNo']; ?>"  autocomplete="off" />
          </div>
        </div>
        <!-- <div class="col-md-3">
          <div >
            <h6 style="font-weight: initial;">Fund Type</h6>
            <select class="inp-w ui-select" name="FundType" >
              <option value="">All</option>
              <option value="Expenditure" <?php // if($_GET['FundType']=="Expenditure"){ echo "selected"; } ?>>Expenditure</option>
              <option value="Sales Return" <?php // if($_GET['FundType']=="Sales Return"){ echo "selected"; } ?>>Sales Return</option>
              <option value="Purchase Return" <?php // if($_GET['FundType']=="Purchase Return"){ echo "selected"; } ?>>Purchase Return</option>
              <option value="Contra" <?php // if($_GET['FundType']=="Contra"){ echo "selected"; } ?>>Contra</option>
            </select>
          </div>
        </div> -->
        <div class="col-md-3">
          <div >
            <h6 style="font-weight: initial;">Transaction Date</h6>
            <input type="text" name="TransactionDate" id="TransactionDate"  class="inp-w ui-select datepicker" style="background:#f7f7f7;">
          </div>
        </div>
        <div class="col-md-2">
          <div>
            <h6>&nbsp;</h6>
            <input type="hidden" id="action" name="action" value="searchaction" />
            <input type="submit" name="Search" class="inp-w ui-select btn btn-success" value="Search" />
          </div>
        </div>
      </div>
      </form>
      <div class="row gy-bvc nn-mb">
        <div class="col-md-12">
          <div class="row lk-kl">
            <div class=""> <a href="addBankPayment.php">
              <button type="button" class="btn btn-default browsebutton pd-btns">Add Entry</button>
              </a></div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid" style="margin-top:20px">
      <div id="tabledata" style="padding: 10px;">
        <?php  if(isset($_SESSION['error'])!=''){ ?>
        <div class="bs-example" id="messageDiv">
          <!-- Success Alert -->
          <div class="alert alert-dismissible fade show" style="border: solid 2px; border-block-color: green; font-weight: 800; font-size: 17px; color: green;"> <?php echo $_SESSION['error'];unset($_SESSION['error']); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        </div>
        <?php } ?>
        <form method="post" id="appfrom">
          <div id="approvebutton" style="display:none; float:left;">
            <button type="button" class="btn btn-success" style="font-size: 13px; font-weight: 700;" onClick="processBar();confirmsubmit();" >Approve</button>
          </div>
          <input type="hidden" name="action" value="approveall">
          <table border="1" class="table" style="width:100%; border:1px solid #ccc;">
            <thead>
              <tr class="hgt" style="background-color: #f5f5f5;border-bottom: 1px solid #ccc;">
                <th>Date</th>
                <th>Voucher No.</th>
                <th>Narration</th>
                <th>Account</th>
                <th>Debit</th>
                <th>Credit</th>
                </tr>
            </thead>
            <tbody id="tablesearch">
              <?php
    if(isset($accountData->status)=='true'){
    if(isset($accountData->VoucherData)){
    $no=1;
	$arrData='';
	foreach($accountData->VoucherData as $resultList){

  $value =  get_object_vars($resultList->ListOfArray);

  ?>
              <tr class="uyt hgte" style="background: #e0f5e0;font-size: 12px !important;border-bottom: 1px solid #ccc;">

                <td><?php echo date('d-M-Y',strtotime($resultList->TransactionDate)); ?></td>
				 <td><?php echo $resultList->VoucherNo; ?></td>
                <td>-</td>
                <td><?php echo $value['AccountName']; ?></td>
                <td>-</td>
                <td><?php echo $value['Amount']; ?></td>
                <!--<td><a href="addBankVoucherEntry.php"><i class="fa fa-pencil" style="font-size: 18px; color:#0000FF;"></i></a> <a href="listBankVoucherEntry.php?type=<?php echo encode("delete"); ?>&did=<?php echo encode($resultList->Id); ?>&action=searchaction&branchCode=<?php echo $_GET['branchCode']; ?>&productType=<?php echo $_GET['productType']; ?>" onClick="return  confirm('are you sure you want to delete?');"><i class="fa fa-trash" style="font-size: 18px; color:#FF0000;"></i></a> </td>-->
              </tr>
              <?php
			foreach($value['listOfData'] as $arrData){
      ?>
              <tr class="uyt hgte" style="font-size: 12px !important;border-bottom: 1px solid #ccc;">
                <td></td>
                <td></td>
                <td><a style="color:#0000FF; " href="#" data-toggle="modal" data-target="#modalpop" onClick="opmodalpop('Narration','modelpop.php?action=showJournalNarration&ndetail=<?php echo htmlentities($arrData->Narration); ?>','100%','auto');"><?php echo $arrData->Narration; ?></a></td>
                <td><?php echo $arrData->AccountName; ?></td>
                <td><?php echo $arrData->Debit; ?></td>
                <td>-</td>
              </tr>
              <?php
            }


    $no++;
   }

   ?>
              <?php


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
    dateFormat: 'yy-mm-dd',
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
