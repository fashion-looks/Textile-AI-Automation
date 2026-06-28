<?php
include "inc.php"; 


if($_REQUEST['action']=="qcrejectionsave"){

$count = $_POST['count'];
$fiedlName = $_POST['FieldName'];
$fieldId = $_POST['fieldId'];
$reasonid = $_POST['ReasonId'];
$resonDescription = $_POST['ReasonName'];
$remarks = $_POST['Remarks'];
$status = $_POST['status'];
$rejuid = $_SESSION['UID'];
$rejdatetime = date('Y-m-d');
$resdatetime = '';
$resuid = '';


$jsonData = json_decode($_POST['reasonlistjson'],true);

$newjson = '{
	"srno":"'.$count.'",
	"fieldId":"'.$fieldId.'",
	"fiedlName":"'.$fiedlName.'",
	"reasonid":"'.$reasonid.'",
	"resonDescription":"'.$resonDescription.'",
	"rejuid":"'.$rejuid.'",
	"remarks":"'.$remarks.'",
	"rejdatetime":"'.$rejdatetime.'",
	"resdatetime":"'.$resdatetime.'",
	"resuid":"'.$resuid.'",
	"status":"'.$status.'"
}';



$rejectionlistn=''; 
if($jsonData['rejectionlist']!=''){
	//$srno = $arrcount;
	foreach($jsonData['rejectionlist'] as $rejectionlist){
		$rejectionlistn.= '{
			"srno":"'.$rejectionlist['srno'].'",
			"fieldId":"'.$rejectionlist['fieldId'].'",
			"fiedlName":"'.$rejectionlist['fiedlName'].'",
			"reasonid":"'.$rejectionlist['reasonid'].'",
			"resonDescription":"'.$rejectionlist['resonDescription'].'",
			"rejuid":"'.$rejectionlist['rejuid'].'",
			"remarks":"'.$rejectionlist['remarks'].'",
			"rejdatetime":"'.$rejectionlist['rejdatetime'].'",
			"resdatetime":"",
			"resuid":"'.$rejectionlist['resuid'].'",
			"status":"'.$rejectionlist['status'].'"
		},';
//$srno++;	
	}
	
}
$rejectionlistfinal = $rejectionlistn.$newjson;
 
if($jsonData['rejectionlist']!=''){
$acknowledgenumber = $jsonData['acknowledgenumber'];
$userid = $jsonData['userid'];
}else{
$acknowledgenumber = $_POST['acknowledgenumber'];
$userid = $_SESSION['UID'];
}


$compJson = '{
	"acknowledgenumber":"'.$acknowledgenumber.'",
	"userid":"'.$userid.'",
	"Rejectionid":"'.$jsonData['Rejectionid'].'",
	"rejectionhash":"'.$jsonData['rejectionhash'].'",
	"rejectionlist":['.$rejectionlistfinal.']
}';


echo $compJson;


?>

<?php
}


if($_REQUEST['action']=="qcresolvesave"){
$srno = $_POST['srno'];
$fiedlName = $_POST['FieldName'];
$fieldId = $_POST['fieldId'];
$reasonid = $_POST['ReasonId'];
$resonDescription = $_POST['ReasonName'];
$remarks = $_POST['Remarks'];
$status = $_POST['status'];
$rejuid =  $_POST['rejuid'];
$rejdatetime =  $_POST['rejdatetime'];
$resdatetime =  $_POST['resdatetime'];
$resuid =  $_POST['resuid'];
$arrid =  $_POST['arrid'];



$newjson = '{
	"srno":"'.$srno.'",
	"fieldId":"'.$fieldId.'",
	"fiedlName":"'.$fiedlName.'",
	"reasonid":"'.$reasonid.'",
	"resonDescription":"'.$resonDescription.'",
	"rejuid":"'.$rejuid.'",
	"remarks":"'.$remarks.'",
	"rejdatetime":"'.$rejdatetime.'",
	"resdatetime":"'.$resdatetime.'",
	"resuid":"'.$resuid.'",
	"status":"'.$status.'"
}';


$jsonData = json_decode($_POST['reasonlistjson'],true);

$rejectionlistn=''; 
if($jsonData['rejectionlist']!=''){
$i=0;

	foreach($jsonData['rejectionlist'] as $rejectionlist){
	
		if($srno!=$rejectionlist['srno']){
			$rejectionlistn.= '{
				"srno":"'.$rejectionlist['srno'].'",
				"fieldId":"'.$rejectionlist['fieldId'].'",
				"fiedlName":"'.$rejectionlist['fiedlName'].'",
				"reasonid":"'.$rejectionlist['reasonid'].'",
				"resonDescription":"'.$rejectionlist['resonDescription'].'",
				"rejuid":"1",
				"remarks":"'.$rejectionlist['remarks'].'",
				"rejdatetime":"'.$rejectionlist['rejdatetime'].'",
				"resdatetime":"",
				"resuid":"'.$rejectionlist['resuid'].'",
				"status":"'.$rejectionlist['status'].'"
			},';
	}
$i++;		}
	
}
$rejectionlistfinal = $rejectionlistn.$newjson;
 
if($jsonData['rejectionlist']!=''){
$acknowledgenumber = $jsonData['acknowledgenumber'];
$userid = $jsonData['userid'];
}else{
$acknowledgenumber = $_POST['acknowledgenumber'];
$userid = $_SESSION['UID'];
}


$compJson = '{
	"acknowledgenumber":"'.$acknowledgenumber.'",
	"userid":"'.$userid.'",
	"Rejectionid":"'.$jsonData['Rejectionid'].'",
	"rejectionhash":"'.$jsonData['rejectionhash'].'",
	"rejectionlist":['.$rejectionlistfinal.']
}';


echo $compJson;


?>

<?php
}


if($_REQUEST['action']=="saveall"){

$reasonlistjsonall = $_POST['reasonlistjsonall'];
$ProductType = trim($_POST['ProductType']);
$resolutionDate = trim($_POST['resolutionDate']);
$inwardDate = trim($_POST['inwardDate']);
logger("JSON to post for Qc Rejection List for ".$ProductType.": ".$reasonlistjsonall);


if($ProductType=='TAN'){
$url = $serverurlapi."HOOperation/RejectionUpdateTan.php?resolutionDate=".$resolutionDate.'&inwardDate='.$inwardDate;
}else{
$url = $serverurlapi."HOOperation/RejectionUpdate.php?resolutionDate=".$resolutionDate.'&inwardDate='.$inwardDate;
}

logger("  API call to save the QC rejection data ". $url); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $reasonlistjsonall);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
echo $response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response,true);


logger("  Response return from rejection save:". $response); 

}

if($_REQUEST['action']=="selfassignment"){

$selfAssign = trim($_REQUEST['selfAssign']);
$productType = trim($_REQUEST['productType']);
$formType = trim($_REQUEST['formType']);
$acknowledgementNo = trim($_REQUEST['acknowledgementNo']);
$userId = trim($_REQUEST['userId']);
$UserType = trim($_REQUEST['UserType']);
$redirecturl = trim($_REQUEST['redirecturl']);

if($selfAssign=='single'){
	$jsonPost = '{
						"status":"single",
						"listofAck":[
							{
								"acknowledgmentno":"'.$acknowledgementNo.'",
								"userId":"'.$userId.'",
								"productType":"'.$productType.'",
								"UserType":"'.$UserType.'"
							}
						]
					}';
}else{
	$jsonPost = '{
					"status":"all",
					"userId":"'.$userId.'",
					"productType":"'.$productType.'",
					"formType":"'.$formType.'",
					"listofAck":[]
				}';
}

$hiturl = $serverurlapi."General/subscribeAPI.php";
$response = postCurlData($hiturl,$jsonPost);
logger("RESPONCE RETURN SLEF ASSIGNMENT API: ". $response); 
$responseData = json_decode($response);
$message = $responseData->Message;

if($selfAssign=='single'){
	logger("Dashboard load for single assignment"); 
	
	
	if($message=="Successfully Assigned"){
	
	?>
<script src="js/jquery.min.js"></script>
<script>
parent.window.location.href = '<?php echo $redirecturl; ?>';
</script>
<?php
	}else{
	$_SESSION['error'] = '['.$acknowledgementNo.'] '.$message;
?>
<script src="js/jquery.min.js"></script>
<script>
parent.funcLoadTable();
</script>
<?php
	}	
}else{
	logger("Dashboard load for all assignment"); 
	$_SESSION['error'] = $message;
?>
<script src="js/jquery.min.js"></script>
<script>
parent.funcLoadTable();
</script>
<?php
}


}

if($_REQUEST['action']=="selfassignmentvendor"){
$userId = trim($_POST['userId']);
$vendorCode = trim($_POST['vendorCode']);

$jsonPost = '{
			"userId":"'.$userId.'",
			"CodeId":"'.$vendorCode.'"
		}';


$hiturl = $serverurlapi."General/vendorAssignAPI.php";
$response = postCurlData($hiturl,$jsonPost);
logger("RESPONCE RETURN VENDOR SLEF ASSIGNMENT API: ". $response); 
$responseData = json_decode($response);
$message = $responseData->Message;

logger("Dashboard load for vendor self assignment"); 
$_SESSION['error'] = $message;
?>
<script src="js/jquery.min.js"></script>
<script>
parent.funcLoadTable();
</script>
<?php
}

if($_REQUEST['act']=="accountApprove"){
$ackno = trim($_REQUEST['ackno']);

$jsonPost = '{
			"AcknowledgementNo":"'.$ackno.'",
			"ProductType":"PAN"
		}';


$hiturl = $serverurlapi."General/updateAccountAPI.php";
$response = postCurlData($hiturl,$jsonPost);
logger("RESPONCE RETURN VENDOR SLEF ASSIGNMENT API: ". $response);
$responseData = json_decode($response);
$Message = $responseData->Message;
$Status = $responseData->Status;
$_SESSION['error'] = ' Ack. No. '.$ackno.' '.$Message;
if($Status==0){
?>
<script src="js/jquery.min.js"></script>
<script>
parent.$("#hide<?php echo $ackno; ?>").css('display', 'none');
parent.$("#show<?php echo $ackno; ?>").css('display', 'block');
</script>
<?php
}else{
?>
<script>
alert('<?php echo $Message; ?>');
</script>
<?php
}
}

if($_REQUEST['act']=="commissionApprove"){
$ackno = trim($_REQUEST['ackno']);
$commissionType = trim($_REQUEST['commissionType']);

$jsonPost = '{
			"AcknowledgementNo":"'.$ackno.'",
			"ProductType":"PAN",
			"commissionType":"'.$commissionType.'"
		}';


$url = $serverurlapi."General/updateCommissionAPI.php";
$response = postCurlData($url,$jsonPost);
logger("RESPONCE RETURN from commission approve API: ". $response);
$responseData = json_decode($response);
$Message = $responseData->Message;
$Status = $responseData->Status;
$_SESSION['error'] = ' Ack. No. '.$ackno.' '.$Message;
if($Status==0){
?>
<script src="js/jquery.min.js"></script>
<script>
parent.$("#hide<?php echo $ackno; ?>").css('display', 'none');
parent.$("#show<?php echo $ackno; ?>").css('display', 'block');
</script>
<?php
}else{
?>
<script>
alert('<?php echo $Message; ?>');
</script>
<?php
}
}

if($_REQUEST['act']=="walletApprove"){
$voucher = trim($_REQUEST['voucher']);

$jsonPost = '{
			"voucher":"'.$voucher.'"
		}';


$url = $serverurlapi."General/updateRechargeAPI.php";
$response = postCurlData($url,$jsonPost);
logger("RESPONCE RETURN from Update Recharge Approve API: ". $response);
$responseData = json_decode($response);
$Message = $responseData->Message;
$Status = $responseData->Status;
$_SESSION['error'] = ' Ack. No. '.$ackno.' '.$Message;
if($Status==0){
?>
<script src="js/jquery.min.js"></script>
<script>
parent.$("#hide<?php echo $voucher; ?>").css('display', 'none');
parent.$("#show<?php echo $voucher; ?>").css('display', 'block');
</script>
<?php
}else{
?>
<script>
alert('<?php echo $Message; ?>');
</script>
<?php
}
}

if($_REQUEST['action']=="schememaster"){
	
	$SchemeName = trim($_POST['SchemeName']);
	$Status = trim($_POST['Status']);
	$CreatedBy = trim($_POST['CreatedBy']);
	$CreatedDate = trim($_POST['CreatedDate']);
	
	$jsonPost = '{
					"SchemeName": "'.$SchemeName.'",
					"Status": "'.$Status.'",
					"CreatedBy": "'.$CreatedBy.'",
					"CreatedDate": "'.$CreatedDate.'"
				}';
	
	$url = $serverurlapi."General/addSchemeMasterAPI.php";
	$response = postCurlData($url,$jsonPost);
	logger("RESPONCE RETURN ADD SCHEME API: ". $response);
	$_SESSION['error'] = $response;

	Header('Location: schememaster.php');
	Exit(); //optional

	}

if($_REQUEST['action']=="schememasterdata"){
	
	$SchemeId = decode($_POST['sid']);
	$Type = trim($_POST['Type']);
	$fromDate = date('Y-m-d',strtotime($_POST['FromDate']));
	$toDate = date('Y-m-d',strtotime($_POST['ToDate']));
	$AppFrom = trim($_POST['AppFrom']);
	$AppTo = trim($_POST['AppTo']);
	$Status = trim($_POST['Status']);
	$Validity = trim($_POST['Validity']);
	$Amount = trim($_POST['Amount']);
	$Status = trim($_POST['Status']);
	$editId = trim($_POST['editId']);
	$CreatedBy = trim($_POST['CreatedBy']);
	
	$jsonPost = '{
					"fromDate": "'.$fromDate.'",
					"toDate": "'.$toDate.'",
					"SchemeId": "'.$SchemeId.'",
					"Type": "'.$Type.'",
					"AppFrom": "'.$AppFrom.'",
					"AppTo": "'.$AppTo.'",
					"Validity": "'.$Validity.'",
					"Amount": "'.$Amount.'",
					"Status": "'.$Status.'",
					"RateId": "'.$editId.'",
					"CreatedBy": "'.$CreatedBy.'"
				}'; 
	
	$url = $serverurlapi."General/addCommissionRateAPI.php";
	$response = postCurlData($url,$jsonPost);
	logger("RESPONCE RETURN FROM ADD SCHEME DATA API: ". $response);
	$_SESSION['error'] = $response;
	Header('Location: schemedata.php?sid='.$_POST['sid'].'&schemeName='.$_POST['schemeName']);
	Exit(); //optional
	
	}

	if($_REQUEST['action']=="cancelack"){
		$ack = trim($_REQUEST['ack']);
		
		$jsonPost = '{
					"AckNumber":"'.$ack.'"
				}';

		$hiturl = $serverurlapi."General/cancellationAPI.php";
		$response = postCurlData($hiturl,$jsonPost);
		logger("RESPONCE RETURN FROM CANCEL ACK API: ". $response); 
		$responseData = json_decode($response);
		$message = $responseData->Message;
		$_SESSION['error'] = $ack.' '.$message;
		?>
<script src="js/jquery.min.js"></script>
<script>
//parent.funcLoadTable();
location.reload();
</script>
<?php
		}


?>