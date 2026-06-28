<?php 
// get url
include "inc.php";
include "logincheck.php";

if($_SESSION['TempBoxId']==''){
	$_SESSION['TempBoxId'] = rand();
}


if($_POST['action']=="getrange"){
	$jsonpost = '{
		"range":"'.$_POST['range'].'",
		"userId":"'.$_SESSION['UID'].'"
	}';

	$url = $serverurlapi."General/rangeAPI.php";
	logger($InfoMessage." json for API - ".$jsonpost); 
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonpost);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$response = curl_exec($ch);
	logger("Response return from range list API: ". $response); 
	$returndata = json_decode($response);
	curl_close($ch);	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title></title>
<meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
<?php include 'links.php'; ?>
<style>
.filterCls{
	padding: 2px;
}
table.dataTable>thead>tr>th:not(.sorting_disabled), table.dataTable>thead>tr>td:not(.sorting_disabled) {
    padding-right: 10px !important;
    padding-left: 10px !important;
}
table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td {
    border-bottom-width: 1px !important;
}
.headline {
    border-bottom: 4px solid #1f7140 !important;
}
.thCls{
	 font-size: 15px;  font-weight: 700; color: #fff;
	 
}
#tableID_length{
 display:none;
}
</style>
<!-- Favicon -->
<link rel="stylesheet" href="css/font-awesome.min.css">
</head>
<body>
<!-- HK Wrapper -->
<div class="hk-wrapper hk-vertical-nav">
  <!-- Top Navbar -->
  <?php include 'backofficeheader.php'; ?>
  <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  <div class="hk-pg-wrapper">
    <div class="container-fluid">
	<form method="post" action="">
      <div class="row gy-bvc">
	  <div class="col-md-4">
          <div class="flx">
            <input type="number" name="range" id="range" value="" placeholder="Enter Range.." class="form-control" onBlur="CheckValidate(this.value);">
			<input type="hidden" name="action" value="getrange">
          </div>
        </div>
		<script>
		function CheckValidate(trange){
			if(trange>100){
				alert('You can not enter range greter than 100');
				location.reload();
			}
			$('#temprange').val(trange);
		}
		</script>
		<div class="col-md-4">
          <div class="flx">
            <button type="submit" class="btn btn-success">Occupy Range</button>
			<p><?php if($returndata->FromRange!=''){ echo 'Receiving Range is: '.$returndata->FromRange.'-'.$returndata->ToRange; }?></p>
          </div>
        </div>
        <div class="col-md-4" style="display:none;">
          <div class="flx">
            <h6 style="font-weight: initial;">Courier&nbsp;Number</h6>
            <select class="inp-w ui-select" name="courierNumber" id="courierNumber">
              <option value="">Select</option>
              <?php
				foreach($dashData->CourierList as $courierNumberData){
				?>
              <option value="<?php echo $courierNumberData->Courier; ?>" <?php if($courierNumberData->Courier==$_GET['courierNumber']){?>selected="selected"<?php } ?> ><?php echo $courierNumberData->Courier; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
	  
	 </form> 
      <div class="row gy-bvc" style="margin-top:25px;">
        <div class="col-md-6">
          <div class="flx">
            <h6 style="font-weight: initial;">Acknowledgement#</h6>
            <input type="number" name="ackNumber" id="ackNumber" class="inp-w ui-select" value="" onBlur="addRowFunc(this.value);">
			<input type="hidden" name="temprange" id="temprange" value="<?php echo $returndata->ToRange-$returndata->FromRange; ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="flx">
            <h6 style="background:#e5e2e2; padding:13px ; border:1px solid #ccc; display:none;" id="matchid"><span style="color:#0abf0a;">Matched, Receiving Number:</span>     <span id="recevingNo"> </span></h6>
			
			<h6 style="background:#e5e2e2; padding:13px ; border:1px solid #ccc; display:none;" id="unmatchid"><span style="color:#bf3c0a;">Unmatched</span></h6>
          </div>
        </div>
		<div class="col-md-2">
          <div class="flx">
          <a href="vendorboxallot.php" class="btn btn-success">Save</a>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid" id="data" style=""><span id="loadingspan" style="display:none;">Loading...</span>
      <div class="col-md-12" id="recorddiv">
        
      </div>
    </div>
  </div>
</div>
</div>
<div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: #000000c7; z-index: 9999; display:none;" id="blkbox">
 
 <div style="padding:20px; background-color:#FFFFFF; margin:auto; width:300px; margin-top:10%; text-align:center; border-radius: 10px;color: green;"><img src="img/Spin2.gif" width="100px;"><br> 
   Updating... Please wait</div>
 </div>
<script>
/*function checkCourier(){
	var courierNumber = $('#courierNumber').val();
	$('#data').load('loadacknumberlist.php?courierNumber='+courierNumber);
	$('#loadingspan').css("display","block");
	$('#recorddiv').css("display","none");
}*/
</script>

<script>
function addRowFunc(ackNo){
	
	if(ackNo!=''){
		var courierNumber = $('#courierNumber').val();
		var TempBoxNumber = '<?php echo $returndata->FromRange.'-'.$returndata->ToRange; ?>';
		var userId = '<?php echo $_SESSION["UID"]; ?>';
		var temprange = $('#temprange').val();
		var postackdata = {
			  acknowledgementNumber:ackNo,
			  tempBoxNumber:TempBoxNumber,
			  courierNo:courierNumber,
			  userId:userId
			}
		
		$('#blkbox').show();
		   // AJAX request
		   $.ajax({
			 url: 'checkacklist.php', 
			 type: 'POST',
			 data: {dataJson: JSON.stringify(postackdata)},
			 dataType: 'json',
			 success: function (response){
			  console.log(response);
			  $('#blkbox').hide();
			  $('#ackNumber').val("");
			  if(response.Count<=temprange){
				  if(response.Result=="Match"){
					$('#matchid').css("display","block");
					$('#recevingNo').text(response.ReceivingNumber);
					$('#unmatchid').css("display","none");
					
				  }else{
					$('#unmatchid').css("display","block");
					$('#matchid').css("display","none");
				  }
			  
				  $('#norecord').hide();
				  $('#addackrow').append('<tr><td>'+response.AckNumber+'</td><td>'+response.ReceivingNumber+'</td><td>'+response.tempBoxNumber+'</td></tr>');
			  }else{
			  	  alert("You can not add receiving more than your range.");
				  window.location.href = 'vendorboxallot.php';
			  }
			 },
			 error:function(jqXhr, textStatus, exception){
				alert('Exeption: '+exception+ jqXhr+ textStatus);
			 }
		   });
	}else{
		alert('Please enter ack number!');
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
	.flx{
	display: flex;
	column-gap: 12px;
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
<?php include 'footer.php'; ?>
</body>
</html>
<!--search filter-->
