<?php 
// get url 
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;


if($_POST['action']=="searchaction"){

$fromDate = trim($_POST['fromDate']);
$toDate = trim($_POST['toDate']);
$ackno = trim($_POST['ackno']);
$branchcode = trim($_POST['branchcode']);
  
$searching = '{
  	"fromDate":"'.date('Y-m-d',strtotime($fromDate)).'",
    "toDate":"'.date('Y-m-d',strtotime($toDate)).'",
    "acknowledgement":"'.$ackno.'",
    "branchcode":"'.$branchcode.'"
}';

$url = $serverurlapi."General/accountReportAPI.php";
$response = postCurlData($url,$searching);
logger('RESPONSE RETURN FROM LIST ACCOUNT SEARCH: '.$response); 
$accountData = json_decode($response, true);
}

if($_POST['action']=='approveall'){
 	$ackJson = '';
	foreach($_POST['acknowledgmentchecksingle'] as $AcknowledgementNumber){
		$ackJson.= '{
				"AcknowledgementNumber":"'.$AcknowledgementNumber.'"
			},';
	}
	
	$jsonPost = '{
		"userId": "'.$_SESSION['UID'].'",
		"ListOfACKO":['.rtrim($ackJson,',').']
	}';
	
	$url =  $serverurlapi."General/updateAccountAPI.php";
	$response = postCurlData($url,$jsonPost);
	logger('RESPONSE RETURN FROM APPROVE ACCOUNT SEARCH: '.$response); 
	$responseData = json_decode($response);
	$Message = $responseData->Message;
	$Status = $responseData->Status;
	if($Status == 0){
		$_SESSION['error'] = 'Record Approved Successfully.';
	}else{
		$_SESSION['error'] = 'Error in Approve.';
	}
	
	
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>List Account</title>
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
      <form action="" method="POST" autocomplete="nope" id="exportfrm" />
      <div class="row gy-bvc">
        <div class="col-md-2">
          <div >
            <h6 style="font-weight: initial;">From&nbsp;Date</h6>
            <input type="text" name="fromDate" class="form-control datepicker" value="<?php echo $_POST['fromDate']; ?>"  autocomplete="off" />
          </div>
        </div>
        <div class="col-md-2">
          <div>
            <h6 style="font-weight: initial;">To&nbsp;Date</h6>
            <input type="text" name="toDate" class="form-control datepicker" value="<?php echo $_POST['toDate']; ?>"  autocomplete="off" />
          </div>
        </div>
        <div class="col-md-2">
          <div >
            <h6 style="font-weight: initial;">Acknowledgement No</h6>
            <input type="text" name="ackno" class="form-control " value="<?php echo $_POST['ackno']; ?>"  autocomplete="off" />
          </div>
        </div>
        <div class="col-md-2">
          <div >
            <h6 style="font-weight: initial;">Branch Code</h6>
            <input type="text" name="branchcode" class="form-control " value="<?php echo $_POST['branchcode']; ?>"  autocomplete="off" />
          </div>
        </div>
        <div class="col-md-2">
          <div>
            <h6>&nbsp;</h6>
            <input type="hidden" id="action" name="action" value="searchaction" />
            <input type="submit" name="Search" class="btn btn-success"  value="Search" onClick="processBar();" />
          </div>
        </div>
      </div>
      </form>
    </div>

    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
	
	

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
        <form method="post" action="">
          <div id="approvebutton" style="display:none; float:left;">
            <button type="submit" class="btn btn-success" style="font-size: 13px; font-weight: 700;" onClick="processBar();" >Approve</button>
          </div>
          <input type="hidden" name="action" value="approveall">
          <table id="datatable" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr class="vcx-i hgt">
                <th><input  name="ackknowledmentCheckAll" type="checkbox" class="" id="ackknowledmentCheckAll" style="height: 17px;width: 50px;margin-top: 0;text-align: center;" /></th>
                <th>Acknowledgement&nbsp;No</th>
                <th>Product&nbsp;Type</th>
                <th>Branch&nbsp;Code</th>
                <th>Fees</th>
                <th>Date&nbsp;Added</th>
                <!--<th>Action</th>-->
              </tr>
            </thead>
            <tbody id="tablesearch">
              <?php
    if(isset($accountData['status'])=='true'){
    if(isset($accountData['accountData'])){                    
    $no=1;
    foreach($accountData['accountData'] as $resultList){

    ?>
              <tr class="uyt hgte">
                <td><input type="checkbox" style=" opacity:1; cursor: pointer; height: 17px; width: 50px; margin-top: 3px;" value="<?php echo $resultList['AckNumber']; ?>" name="acknowledgmentchecksingle[]"  class="deleteack"/></td>
                <td><?php echo $resultList['AckNumber']; ?></td>
                <td><?php echo $resultList['ProductType']; ?></td>
                <td><?php echo $resultList['BranchCode']; ?></td>
                <td><?php echo round(substr($resultList['Fees'],1)); ?></td>
                <td><?php echo date('d-m-Y',strtotime($resultList['DateAdded'])); ?></td>
                <!--<td><div id="hide<?php echo $resultList['AckNumber']; ?>" onClick="funccheck(<?php echo $no; ?>,'<?php echo $resultList['AckNumber']; ?>');" style="cursor: pointer;color: white;background: #2f9e41;border: 1px solid #2f9e41;border-radius: 4px;padding: 3px 0px; <?php  if($resultList['FeeFlag'] == 0){ ?>display: block; <?php }else{ ?>display: none;<?php } ?>">Approve</div>
                  <div id="show<?php echo $resultList['AckNumber']; ?>" style="color: white;font-size: 13px;background: #1f7140;width: fit-content;padding: 1px 5px;border-radius: 50%;display: block;margin: auto; <?php  if($resultList['FeeFlag'] == 0){ ?>display: none; <?php }else{ ?>display: block;<?php } ?>"><i class="fa fa-check"></i></div></td>-->
              </tr>
              <?php
    $no++;
  }
}
  }
  else{?>
              <tr class="uyt hgte">
                <td colspan="11"><div align="center"><?php echo 'You Can Search...'; ?></div></td>
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
 



<script type="text/javascript">
  
function funccheck(no,ackno){
  var confirmed = confirm("Do you want to Approve Acknowledgement no "+ackno);
  if(confirmed){
    $('#hide'+ackno).load('frmaction.php?act=accountApprove&ackno='+ackno);
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
