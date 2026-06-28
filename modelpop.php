
<?php
include("inc.php");
include "logincheck.php";

if($_REQUEST['action']=="exportpopup"){
	$batchId = $_REQUEST['batchId'];
?>
<script src="js/jquery.min.js"></script>
<div class="container">
	<div class="form-group">
		<table class="table">
			<thead id="myTable">
				<tr class="">
				<th><p style="padding: 15px; border: 1px solid #ccc;">abc test</p></th>
				</tr>
				<tr class="">
				<th><p style="padding: 15px; border: 1px solid #ccc;">xxx</p></th>
				</tr>
				<tr class="">
				<th><p style="padding: 15px; border: 1px solid #ccc;">ccc</p></th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<?php
}

if($_REQUEST['action']=="addqcrejection"){
$aid = trim($_REQUEST['aid']);
$formType = trim($_REQUEST['formType']);
$status = trim($_REQUEST['status']);
$ProductType = trim($_REQUEST['ProductType']);
//$formType = '49A';

$ackpost = '{
    "acknowledgenumber":"'.$aid.'",
    "records":"",
    "rejectionId":""
}';

logger("JSON  Post to get Rejection data for ".$ProductType.": ".$ackpost);
if($ProductType=='TAN'){
$urlpost = $serverurlapi."HOOperation/GetRejctionListTan.php";
}else{
$urlpost = $serverurlapi."HOOperation/GetRejctionList.php";
}

logger(" API url: ". $urlpost);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$urlpost);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ackpost);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

logger("Response return from api----".$response);

$res = json_decode($response,true);


$resultArrData = '';
if($res['result']!=''){
	foreach($res['result'] as $resultArr){

		$rejectionlistArrData='';
		foreach($resultArr['insufData'] as $rejectionlistArr12){
				$rejectionlistArrData.= '{
					"srno": "'.$rejectionlistArr12["srno"].'",
					"fieldId": "'.$rejectionlistArr12["fieldId"].'",
					"fiedlName": "'.$rejectionlistArr12["fiedlName"].'",
					"reasonid": "'.$rejectionlistArr12["reasonid"].'",
					"resonDescription": "'.$rejectionlistArr12["resonDescription"].'",
					"rejuid": "'.$rejectionlistArr12["rejuid"].'",
					"remarks": "'.$rejectionlistArr12["remarks"].'",
					"rejdatetime": "'.$rejectionlistArr12["rejdatetime"].'",
					"resdatetime": "'.$rejectionlistArr12["resdatetime"].'",
					"resuid": "'.$rejectionlistArr12["resuid"].'",
					"status": "'.$rejectionlistArr12["status"].'"
				},';


		}

		$resultArrData.='{
							"acknowledgenumber":"'.$res['acknowledgementNumber'].'",
							"userid":"'.$_SESSION['UID'].'",
							"Rejectionid":"'.$resultArr['rejectionId'].'",
							"rejectionhash":"",
							"rejectionlist":['.rtrim($rejectionlistArrData,',').']
						}';



	}
}
$_SESSION['reasonlist'] = $resultArrData;

?>
<style>
.searchCls{
	display:none;
}
</style>
<script src="js/md5.min.js"></script>
<form class="form-horizontal" name="formaction" id="qcform"   target="actoinfrm">
<div class="container">

	<div class="row" style="padding-top:10px;">
	<div class="col-md-4">
	<label class="control-label col-sm-2" for="email" style="padding: 0px;">Field&nbsp;Name</label>
    <div class="form-group"><!-----onchange="funAddReason(this.value);"--->
     <select name="FieldName" id="FieldName" class="form-control"  required>
			<option value="">Select</option>
		   <?php
			$url = $serverurlapi."General/rejectioninfoList.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			$data = json_decode($result);
			curl_close($ch);
			//logger(" Rejection Field response: ". $result);
			logger(" Form Type: ". $formType. "Product Type: ".$ProductType);
			foreach($data->Rejectionlist as $RejectionlistArr){
			if($RejectionlistArr->FormType==$formType && $RejectionlistArr->ProductType==$ProductType){

			foreach($RejectionlistArr->FieldList as $FieldNameData){
			if($FieldNameData->Status==1){
			?>
			<option value="<?php echo $FieldNameData->FieldName; ?>" mytag="<?php echo $FieldNameData->Masterid; ?>" ><?php echo $FieldNameData->FieldName; ?></option>
		  <?php } }  }

		   } ?>
	</select>
	<input type="hidden" name="fieldId" id="fieldId" value="">
      </div>
    </div>
	<div class="col-md-4">
	<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Rejection&nbsp;Reason</label>
    <div class="form-group">
      <select name="ReasonName" id="ReasonName" class="form-control" required>

	  </select>
	  <input type="hidden" name="acknowledgenumber" id="acknowledgenumber" value="<?php echo $aid; ?>">
	  <input type="hidden" name="ReasonId" id="ReasonId" value="">
	  <input type="hidden" name="status" id="status" value="0">
	  <input type="hidden" name="reasonlistjson" id="reasonlistjson" value="<?php echo htmlentities($_SESSION['reasonlist']); ?>">
	  <input type="hidden" name="rejectionhash" id="rejectionhash" value="<?php echo htmlentities($_SESSION['reasonlist']); ?>">
	  <input type="hidden" name="count" id="count" value="1">
      </div>
    </div>
<script>
$(function() {
    $("#FieldName").change(function(){
        var element = $(this).find('option:selected');
        var myTag = element.attr("myTag");

		$('#ReasonName').load('loadreason.php?action=loadreason&id='+myTag+'&formType=<?php echo $formType; ?>');
        $('#fieldId').val(myTag);
    });

    $("#ReasonName").change(function(){
        var element = $(this).find('option:selected');
        var myTag = element.attr("myTag");

        $('#ReasonId').val(myTag);

		var getnewrejectval = $("#reasonlistjson").val();
		if(getnewrejectval!=''){
			var newreasonlistjson = JSON.parse($("#reasonlistjson").val());
			lengthnewreason = newreasonlistjson.rejectionlist.length;
			lengthnewreason = lengthnewreason+1;

			$('#count').val(lengthnewreason);
		}



    });

});

</script>
	<div class="col-md-4">
	<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Remarks</label>
    <div class="form-group">
      <input type="text" name="Remarks" id="Remarks" class="form-control" />
      </div>
    </div>
    </div>

</div>
<div class="modal-footer">
	<button type="submit" id="addBtn" class="btn btn-success" >Add</button>
</div>
 </form>
<script>
$(function () {
	$('#qcform').on('submit', function (e) {
	 var ReasonName = $("#ReasonName").val();
  	 var FieldName = $("#FieldName").val();
	 var Remarks = $("#Remarks").val();

	 var form_data = $(this).serialize(); //Encode form elements for submission

		//e.preventDefault();
		  $.ajax({
		  url: 'frmaction.php?action=qcrejectionsave',
			type: 'POST',
			data : $(this).serialize(),
			success: function (response) {
				console.log(response);
				var arr = '['+response+']';
				arr =arr;

				$('#reasonlistjson').val(response);
				$('#reasonlistjsonall').val(response);
				$('#loadrejectiondata').append("<tr><td>"+FieldName+"</td><td>"+ReasonName+"</td><td>"+Remarks+"</td><td></td></tr>");
				 $("#ReasonName").val("");
				 $("#FieldName").val("");
				 $("#Remarks").val("");

				var reasonlistjson = JSON.parse($("#reasonlistjson").val());

				//console.log(reasonlistjson.rejectionlist.length);

				var rejectionhash = $("#rejectionhash").val();


				var reasonlistjsonPretty = JSON.stringify(reasonlistjson, null, '\t');
				var rejectionhashjsonPretty = JSON.stringify(rejectionhash, null, '\t');

				var reasonlistjsonmd5 = md5(reasonlistjsonPretty);
				var rejectionhashmd5 = md5(rejectionhashjsonPretty);

				if(reasonlistjsonmd5!=rejectionhashmd5){
					$(".closeBtn").removeAttr("data-dismiss");
				}

			}
		});
	});
});
</script>
<div style="height:215px; overflow:auto;">
  <table class="table" style="">
<thead>
<tr class="headline">
<th>Field Name</th>
<th>Reason</th>
<th>Remark</th>
<th>Rejection Id</th>
</tr>
</thead>
<tbody id="loadrejectiondata">
	<?php
	if($_SESSION['reasonlist']!=''){
	$datalist = json_decode($_SESSION['reasonlist']);
	foreach($datalist->rejectionlist as $reasondata){
	?>
	<tr>
		<td><?php echo $reasondata->fiedlName; ?></td>
		<td><?php echo $reasondata->resonDescription; ?></td>
		<td><?php echo $reasondata->remarks; ?></td>
		<td><?php echo $datalist->Rejectionid; ?></td>
	</tr>
	<?php } } ?>
</tbody>
</table>
</div>
<form name="saveform" id="saveform" target="actoinfrm">

<input type="hidden" name="reasonlistjsonall" id="reasonlistjsonall" value="" />
<input type="hidden" name="action"  value="saveall" />
<input type="hidden" name="ProductType"  value="<?php echo $ProductType; ?>" />
 <div class="modal-footer">
	<button type="submit"  id="finalSaveButton" class="btn btn-success" >Save</button>
    <button type="button" class="btn btn-default closeBtn" data-dismiss="modal">Close</button>
</div>

</form>

<script>
$(function () {
	$('#saveform').on('submit', function (e) {
	 	//e.preventDefault();
		  $.ajax({
		  	url: 'frmaction.php?action=saveall',
			type: 'POST',
			data : $(this).serialize(),
			success: function (response) {
				console.log(response);
				arr = JSON.parse(response);
				if(arr.RecordUpdated=='1'){
					arr = JSON.parse(response);
					alert('Data save succesfully.');
					parent.$('#intimationmodalBtn').hide();
					parent.$('#intimationBtn').show();
					$(".closeBtn").attr("data-dismiss","modal");
					$('.closeBtn').click();

				}
			}
		});
	});
});


$(function () {
	$('.closeBtn').on('click', function () {
		if ($(this).attr('data-dismiss')!='modal') {
			alert('Please save the reason list else it will lost newly added data!');
		}
	});
});
</script>

<?php
}

if($_REQUEST['action']=="addqcresolve"){
$aid = trim($_REQUEST['aid']);
$formType = trim($_REQUEST['formType']);
$status = trim($_REQUEST['status']);
$formType = '49A';
$ProductType = trim($_REQUEST['ProductType']);

$ackpost = '{
    "acknowledgenumber":"'.$aid.'",
    "records":"",
    "rejectionId":""
}';

logger("JSON  Post to get Rejection data for ".$ProductType.": ".$ackpost);

if($ProductType=='TAN'){
$urlpost = $serverurlapi."HOOperation/GetRejctionListTan.php";
}else{
$urlpost = $serverurlapi."HOOperation/GetRejctionList.php";
}

logger(" API url: ". $urlpost);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$urlpost);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ackpost);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

logger("Response return from api----".$response);

$res = json_decode($response,true);


$resultArrData = '';
if($res['result']!=''){
	foreach($res['result'] as $resultArr){

		$rejectionlistArrData='';
		foreach($resultArr['insufData'] as $rejectionlistArr12){
				$rejectionlistArrData.= '{
					"srno": "'.$rejectionlistArr12["srno"].'",
					"fieldId": "'.$rejectionlistArr12["fieldId"].'",
					"fiedlName": "'.$rejectionlistArr12["fiedlName"].'",
					"reasonid": "'.$rejectionlistArr12["reasonid"].'",
					"resonDescription": "'.str_replace("'",'\"',$rejectionlistArr12["resonDescription"]).'",
					"rejuid": "'.$rejectionlistArr12["rejuid"].'",
					"remarks": "'.$rejectionlistArr12["remarks"].'",
					"rejdatetime": "'.$rejectionlistArr12["rejdatetime"].'",
					"resdatetime": "'.$rejectionlistArr12["resdatetime"].'",
					"resuid": "'.$rejectionlistArr12["resuid"].'",
					"status": "'.$rejectionlistArr12["status"].'"
				},';


		}

		$resultArrData.='{
							"acknowledgenumber":"'.$res['acknowledgementNumber'].'",
							"userid":"'.$_SESSION['UID'].'",
							"Rejectionid":"'.$resultArr['rejectionId'].'",
							"rejectionhash":"",
							"rejectionlist":['.rtrim($rejectionlistArrData,',').']
						}';



	}

}
$_SESSION['reasonlist'] = $resultArrData;

?>
<style>
.searchCls{
	display:none;
}
</style>
<script src="js/md5.min.js"></script>
<form class="form-horizontal" name="formaction" id="qcform"   target="actoinfrm">
<div class="container">

	<div class="row" style="padding-top:10px;">
	<div class="col-md-4">
	<label class="control-label col-sm-2" for="email" style="padding: 0px;">Field&nbsp;Name</label>
    <div class="form-group"><!-----onchange="funAddReason(this.value);"--->
     <!--<select name="FieldName" id="FieldName" class="form-control"  required style="pointer-events: none;" >
			<option value=""></option>
		   <?php
			$url = $serverurlapi."General/rejectioninfoList.php";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			$data = json_decode($result);
			curl_close($ch);

			foreach($data->Rejectionlist as $RejectionlistArr){
			if($RejectionlistArr->FormType==$formType){
			foreach($RejectionlistArr->FieldList as $FieldNameData){
			?>
			<option value="<?php echo $FieldNameData->FieldName; ?>" mytag="<?php echo $FieldNameData->Masterid; ?>" ><?php echo $FieldNameData->FieldName; ?></option>
		  <?php } }  } ?>
	</select>-->
	<input type="text" name="FieldName" id="FieldName" value=""  class="form-control" readonly>
	<input type="hidden" name="fieldId" id="fieldId" value="">
      </div>
    </div>
	<div class="col-md-4">
	<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Rejection&nbsp;Reason</label>
    <div class="form-group">
      <!--<select name="ReasonName" id="ReasonName" class="form-control" required style="pointer-events: none;">

	  </select>-->
	  <input type="text" name="ReasonName" id="ReasonName" value="" class="form-control" readonly>
	  <input type="hidden" name="acknowledgenumber" id="acknowledgenumber" value="<?php echo $aid; ?>">

	  <input type="hidden" name="ReasonId" id="ReasonId" value="">

	  <input type="hidden" name="reasonlistjson" id="reasonlistjson" value="<?php echo htmlentities($_SESSION['reasonlist']); ?>">
	  <input type="hidden" name="rejectionhash" id="rejectionhash" value="<?php echo htmlentities($_SESSION['reasonlist']); ?>">
      </div>
    </div>
<script>
$(function() {
    $("#FieldName").change(function(){
        var element = $(this).find('option:selected');
        var myTag = element.attr("myTag");

		$('#ReasonName').load('loadreason.php?action=loadreason&id='+myTag+'&formType=<?php echo $formType; ?>');
        $('#fieldId').val(myTag);
    });

    $("#ReasonName").change(function(){
        var element = $(this).find('option:selected');
        var myTag = element.attr("myTag");

        $('#ReasonId').val(myTag);
    });

});

</script>

	<div class="col-md-4">
	<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Resolve</label>
    <div class="form-group">
      <select name="status" id="status" class="form-control">
	  	<option value="1">Yes</option>
		 <option value="0">No</option>
	  </select>
      </div>
    </div>

	<div class="col-md-12">
	<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Remarks</label>
    <div class="form-group">
      <input type="text" name="Remarks" id="Remarks" class="form-control" readonly/>


      </div>
    </div>


    </div>
 	 <input type="hidden" name="rejuid" id="rejuid" class="form-control" value="" />
	  <input type="hidden" name="rejdatetime" id="rejdatetime" class="form-control" value=""/>
	  <input type="hidden" name="resdatetime" id="resdatetime" class="form-control" value="<?php echo date('Y-m-d'); ?>" />
	  <input type="hidden" name="resuid" id="resuid" class="form-control" value="<?php echo $_SESSION['UID']; ?>" />
	  <input type="hidden" name="arrid" id="arrid" class="form-control" value="" />
	  <input type="hidden" name="srno" id="srno" value="">
</div>
<div class="modal-footer">
	<button type="submit" id="addBtn" class="btn btn-success" style="display:none;" >Update</button>
</div>
 </form>
<script>
$(function () {
	$('#qcform').on('submit', function (e) {
	 var ReasonName = $("#ReasonName").val();
  	 var FieldName = $("#FieldName").val();
	 var Remarks = $("#Remarks").val();
	 var NReasonId = $("#ReasonId").val();
	 var srno = $("#srno").val();

	var form_data = $(this).serialize(); //Encode form elements for submission

		//e.preventDefault();
		  $.ajax({
		  url: 'frmaction.php?action=qcresolvesave',
			type: 'POST',
			data : $(this).serialize(),
			success: function (response) {
				console.log(response);
				$('#reasonlistjson').val(response);
				$('#reasonlistjsonall').val(response);
				//$('#loadrejectiondata').append("<tr><td>"+FieldName+"</td><td>"+ReasonName+"</td><td>"+Remarks+"</td><td></td></tr>");
				 $("#ReasonName").val("");
				 $("#FieldName").val("");
				 $("#Remarks").val("");

				 $("#status_"+srno).text('Resolved');
				 $('#finalSaveButton').css('display','block');

				var reasonlistjson = JSON.parse($("#reasonlistjson").val());
				var rejectionhash = JSON.parse($("#rejectionhash").val());

				var reasonlistjsonPretty = JSON.stringify(reasonlistjson, null, '\t');
				var rejectionhashjsonPretty = JSON.stringify(rejectionhash, null, '\t');

				var reasonlistjsonmd5 = md5(reasonlistjsonPretty);
				var rejectionhashmd5 = md5(rejectionhashjsonPretty);

				if(reasonlistjsonmd5!=rejectionhashmd5){
					$(".closeBtn").removeAttr("data-dismiss");
				}

			}
		});
	});
});
</script>
<div style="height:215px; overflow:auto;">
  <table class="table" style="">
<thead>
<tr class="headline">
<th>Field Name</th>
<th>Reason</th>
<th>Remark</th>
<th>Rejection Id</th>
<th>Status</th>
</tr>
</thead>
<tbody id="loadrejectiondata">
	<?php
	if($_SESSION['reasonlist']!=''){
	$datalist = json_decode($_SESSION['reasonlist']);
	$i=0;
	foreach($datalist->rejectionlist as $reasondata){
	$arr = $datalist->rejectionlist[$i];
	?>
	<tr>
		<td><?php echo $reasondata->fiedlName; ?></td>
		<td><?php echo $reasondata->resonDescription; ?></td>
		<td><?php echo $reasondata->remarks; ?></td>
		<td><?php echo $datalist->Rejectionid; ?></td>
		<td><span id="status_<?php echo $reasondata->srno; ?>"><?php if($reasondata->status==0){ echo 'Open'; }else{ echo 'Resolved'; } ?></span></td>
		<td><a class="btn btn-success" onclick="funcEdit('<?php echo $i; ?>');" style="cursor:pointer;">Edit</a></td>
		<input type="hidden" id="edit_<?php echo $i; ?>" value='<?php echo json_encode($arr); ?>' />
	</tr>
	<?php  $i++; }  } ?>
</tbody>
</table>
</div>
<script>
function funcEdit(id){
	var arr = $('#edit_'+id).val();
	var newjson = $.parseJSON(arr);

	var srno = newjson.srno;
	var fieldId = newjson.fieldId;
	var fiedlName = newjson.fiedlName;
	var reasonid = newjson.reasonid;
	var resonDescription = newjson.resonDescription;
	var rejuid = newjson.rejuid;
	var remarks = newjson.remarks;
	var rejdatetime = newjson.rejdatetime;
	var resdatetime = newjson.resdatetime;
	var resuid = newjson.resuid;

	var newf = $('#fieldId').val(fieldId);
	$('#FieldName').val(fiedlName);
	if(newf!=''){
		$('#addBtn').css('display','block');
	}
	var element = $('#FieldName').find('option:selected');
    var myTag = element.attr("myTag");
	$('#ReasonName').load('loadreason.php?action=loadreason&id='+myTag+'&formType=<?php echo $formType; ?>&rid='+encodeURI(resonDescription));

	$('#ReasonId').val(reasonid);
	$('#ReasonName').val(resonDescription);
	$('#rejuid').val(rejuid);
	$('#Remarks').val(remarks);
	$('#rejdatetime').val(rejdatetime);
	$('#arrid').val(reasonid);
	$('#srno').val(srno);

}
</script>
<form name="saveform" id="saveform" target="actoinfrm">

<input type="hidden" name="reasonlistjsonall" id="reasonlistjsonall" value="" />
<input type="hidden" name="action"  value="saveall" />
<input type="hidden" name="ProductType"  value="<?php echo $ProductType; ?>" />
 <div class="modal-footer">
 	<input type="text" name="resolutionDate" id="resolutionDate" class="form-control datepicker" value="" style="width: 170px; display:none;" placeholder="Resolution Date" />
	<input type="text" name="inwardDate" id="inwardDate" class="form-control datepicker" value="" style="width: 170px; display:none;" placeholder="Inward Date" />
	<button type="submit"  id="finalSaveButton" style="display:none;" class="btn btn-success" >Save</button>
    <button type="button" class="btn btn-default closeBtn" data-dismiss="modal">Close</button>
</div>

</form>

<script>
$( function() {
	$( "#resolutionDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		minDate:'-2D',
		maxDate: 0,
		onSelect: function(selected){
			var mindatenew = selected;
			mindatenew = mindatenew.substring(0,2);
			mindatenew = (Number(mindatenew-1))+"<?php echo date('-m-Y');?>";
			$('#inwardDate').datepicker("setDate",mindatenew);
		  }
	});
});

$( function() {
	//var today = new Date();
	//var tomorrow = new Date();
	$( "#inwardDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		minDate:'-3D',
		maxDate: 0
	});
});



$(function () {
	$('#saveform').on('submit', function (e) {
	 	//e.preventDefault();
		  $.ajax({
		  	url: 'frmaction.php?action=saveall',
			type: 'POST',
			data : $(this).serialize(),
			success: function (response) {
				console.log(response);
				arr = JSON.parse(response);
				if(arr.RecordUpdated=='1'){
					arr = JSON.parse(response);
					alert('Data save succesfully.');
					$(".closeBtn").attr("data-dismiss","modal");
					$('.closeBtn').click();

				}
			}
		});
	});
});


$(function () {
	$('.closeBtn').on('click', function () {
		if ($(this).attr('data-dismiss')!='modal') {
			alert('Please save the reason list else it will lost newly added data!');
		}
	});
});
</script>

<?php
}

if($_REQUEST['action']=="submitqc"){
$aid = $_REQUEST['aid'];

$reasondatajson = '{
    "acknowledgenumber":"255369700328632",
    "userid":"",
    "Rejectionid":"123",
    "rejectionhash":"",
    "rejectionlist":[{
                    "fieldId":"11",
                    "fiedlName":"DOB",
                    "reasonid":"PNDOB_1",
                    "resonDescription":"DOB should not be a future Date",
                    "remarks":"test"
										}]
 }';
?>
<style>
.searchCls{
	display:none;
}
</style>
<form class="form-horizontal" name="formaction" id="qcform">
<div class="container">
	<div class="row" style="padding-top:10px;">
		<p></p>
	</div>
</div>
<div class="modal-footer">
	<button type="submit" class="btn btn-success" >Ok</button>
    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
</div>
</form>

<?php
}

if($_REQUEST['action']=="addrejectionreason"){
	$aid = $_REQUEST['aid'];

	$jsonPost = '{"ReqestId":'.$aid.'}';

	logger("JSON to post for ReqestId----".$jsonPost);

	$url = "".$serverurlapi."General/rejectioninfoList.php";

	logger("URL to hit on API----".$url);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPost);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$rejectionresult = curl_exec($ch);
	logger("JSON to post for ReqestId----".$rejectionresult);
	$rejectionData = json_decode($rejectionresult, true);
	$rejectionDataq = $rejectionData['Rejectionlist'];
	$rejectionDataqq = $rejectionDataq[0];
	$rejectionDataqqq = $rejectionDataqq['FieldList'];
	$rejectionDataqqqq = $rejectionDataqqq[0];

	curl_close($ch);
	?>
	<style>
	.searchCls{
		display:none;
	}
	.rejeditbutton{
		background: #63af32;
		color: white;
		border: 1px solid;
		padding: 5px 15px;
		border-radius: 4px;
		outline: none;
	}
	.rejaddbutton{
		border: 1px solid;
		color: white;
		background: #63af32;
		padding: 5px 20px;
		border-radius: 4px;
	}
	.rejsavebutton{
		border: 1px solid;
		padding: 5px 20px;
		background: #2f7800;
		color: white;
		border-radius: 3px;
	}
	.rejdeletebutton{
		border: 1px solid;
		padding: 5px 20px;
		background: #b62c2c;
		color: white;
		border-radius: 3px;
	}
	.rejsavebutton:hover,.rejsavebutton:focus,.rejaddbutton:hover,.rejaddbutton:focus,.rejeditbutton:hover,.rejeditbutton:focus,.rejdeletebutton:hover,.rejdeletebutton:focus{
		color: white;
	}
</style>
<form class="form-horizontal" name="formaction" id="rejectionreasonform" method="post" action="">
	<div class="container">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-4">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Field&nbsp;Name</label>
				<div class="form-group">
					<?php
					$url = "".$serverurlapi."General/createfield.php?status=0&editId=".$aid."";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					$result = curl_exec($ch);
					$editresult = json_decode($result, true);
					curl_close($ch);
					?>
					<input type="text" readonly="readonly" name="Fieldname" id="Fieldname" class="form-control" value="<?php echo $editresult['FieldName'] ?>" />
					<input type="hidden"  name="ProductType" id="ProductType" value="<?php echo $editresult['ProductType'] ?>" />
					<input type="hidden"  name="FormType" id="FormType" value="<?php echo $editresult['FormType'] ?>" />
					<input type="hidden" name="rIndexId" id="rIndexId" value="" />
					<input type="hidden" name="itemId" id="itemId" value="0" />


				</div>
			</div>
			<div class="col-md-3">
				<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">NSDL&nbsp;ID</label>
				<div class="form-group">
					<input type="text" autocomplete="off" name="NsdlId" id="NsdlId" class="form-control" />
				</div>
			</div>
			<div class="col-md-5">
				<label class="control-label col-sm-2" for="pwd"  style="padding: 0px;">Rejection&nbsp;Reason</label>
				<div class="form-group">
					<input type="text" autocomplete="off" name="RejectionReason" id="RejectionReason" class="form-control" />
				</div>
			</div>
		</div>
		<input type="hidden" name="datareturn" value="" id="datareturn" />
	</div>
	<div class="modal-footer">
		<a href="javascript:void(0);" id="rejaddbutton" class="rejaddbutton" onclick="addrejection();">Add</a>
	</div>

	<script>
		function addrejection(){
			var itemId = $("#itemId").val();
			if(itemId == 0)
			{
				var RejectionReason = $("#RejectionReason").val();
				var NsdlId = $("#NsdlId").val();
				// var Id = $("#countId").val();
				// var add = Number(Id)+Number(1);

				$('#loadrejectionreasondata').append("<tr><td style=\"width:25%\">"+NsdlId+"</td><td style=\"width:75%\">"+RejectionReason+"</td><td style=\"display: grid; grid-template-columns: auto auto;\"><input type=\"submit\" class=\"rejeditbutton\" value=\"Edit\" onclick=\"selectedRowInput();\" /><input type=\"submit\" class=\"rejdeletebutton\" value=\"Delete\" onclick=\"deleteRowInput();\" /></td></tr>");
				$("#RejectionReason").val("");
				$("#NsdlId").val("");
				// $('#countId').val(add);

			}
			else{
				var table = document.getElementById("loadrejectionreasondata");
				RejectionReason = document.getElementById("RejectionReason").value;
				NsdlId = document.getElementById("NsdlId").value;
				rIndexId = document.getElementById("rIndexId").value;
				indexitem = rIndexId-1;
				table.rows[indexitem].cells[1].innerHTML = RejectionReason;
				table.rows[indexitem].cells[0].innerHTML = NsdlId;
	   //change value to add
	   $('#itemId').val(0);
	   $('#RejectionReason').val("");
	   $('#NsdlId').val("");
	}
	$('#rejaddbutton').text('Add');
}

function saveRejectionReason(){
	var Id = <?php echo $aid; ?>;
	var product = $('#ProductType').val();
	var form = $('#FormType').val();
	var ProductType = $('#ProductType').val().charAt(0);
	var FormType = $('#FormType').val().charAt(0);
	var Fieldname = $('#Fieldname').val().toUpperCase().substring(0,3);
	var TableData = new Array();
	$('#loadrejectionreasondata tr').each(function(row, tr){
		TableData[row]={
			"id" : ProductType+FormType+Fieldname+'_'+Id+$(tr).find('td:eq(1)').text().length,
			"NsdlId" :$(tr).find('td:eq(0)').text(),
			"description" :$(tr).find('td:eq(1)').text().replaceAll(" ", "%20")
		}
	});
	var arraydata = JSON.stringify(Object.assign([], TableData))

	$.ajax({
		url: "rejectionreasonpost.php?id="+Id+"&product="+product+"&form="+form+"&RejectionReason="+arraydata,
		type: "POST",
		dataType: "json",
		contentType: "application/json; charset=utf-8",
		data: arraydata,
		success: function (result) {
		   console.log(result);
		   if(result==0){
		   	window.location.href = '<?php echo "addFieldList.php?product=".$editresult['ProductType']."&form=".$editresult['FormType']; ?>';
		   }else{
		   	window.location.href = '<?php echo "addFieldList.php?product=".$editresult['ProductType']."&form=".$editresult['FormType']; ?>';
		   }
		},
		error: function (err) {
		// check the err for error details
		}
	}); // ajax call closing

	//$('#saveRejectionData').load('General/saveRejectionReason.php?id='+Id+'&product='+product+'&form='+form+'&RejectionReason='+arraydata);

}

function selectedRowInput(){
	event.preventDefault();
	var table = document.getElementById("loadrejectionreasondata");
	for(var i = 0; i < table.rows.length; i++){
		table.rows[i].onclick = function()
		{
			rIndex = this.rowIndex;
			document.getElementById("NsdlId").value = this.cells[0].innerHTML;
			document.getElementById("RejectionReason").value = this.cells[1].innerHTML;
		  //for update
		  document.getElementById("rIndexId").value = rIndex;
		};
	}
	 //change value to edit button
	 $('#itemId').val(1);
	 $('#rejaddbutton').text('Update');
	}

	function deleteRowInput(){
		event.preventDefault();
		var table = document.getElementById("loadrejectionreasondata");
		for(var i = 0; i < table.rows.length; i++){
			table.rows[i].onclick = function()
			{
				this.remove();
			};
		}
	}
</script>
<div id="saveRejectionData" style="text-align: center;color: #63af32;font-size: 16px;"></div>
<div style="height:215px; overflow:auto;">
<table class="table" style="">
	<thead>
		<tr class="headline">
			<!-- <th>S.No</th>	 -->
			<th>NSDL ID</th>
			<th>Rejection Reason</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody id="loadrejectionreasondata">
		<?php
		$no=1;
		foreach($rejectionDataqqqq['RejectionReason'] as $rejectionList){
			?>
			<tr>
				<!-- <td><?php echo $no; ?></td> -->
				<td style="width:25%"><?php echo $rejectionList['NsdlId']; ?></td>
				<td style="width:75%"><?php echo $rejectionList['description']; ?></td>
				<td>
				<div style="display: grid; grid-template-columns: auto auto;">
				<input type="submit" value="Edit" class="rejeditbutton" style="padding: 0px 15px !important;" onclick="selectedRowInput();" />
					<input type="submit" value="Delete" class="rejdeletebutton" style="padding: 0px 15px !important;" onclick="deleteRowInput();" />
					</div></td>

			</tr>
			<?php $no++; } ?>
		</tbody>
	</table>
</div>
	<!-- <input type="hidden"  name="countId" id="countId" value="<?php echo $no; ?>" /> -->
	<div class="modal-footer">
		<a href="javascript:void(0);" class="rejsavebutton" onclick="saveRejectionReason();">Save</a>
		<button type="button" style="background: gray;color: white;border: 1px solid;padding: 5px 18px;border-radius: 4px;" data-dismiss="modal">Close</button>
	</div>

</form>
<?php
}

if($_REQUEST['action']=="boxallot"){
?>

<div class="container">
	<div class="form-group">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-4">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Vender&nbsp;Name</label>
				<div class="form-group">
				<select name="vendor" id="vendorid" class="form-control">
					<option value="">Select</option>
					<option value="1">Vendor 1</option>
				  </select>
				</div>
			</div>

			<div class="col-md-4">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Box&nbsp;Number</label>
				<div class="form-group">
				<input type="text" name="boxnoid"  id="boxnoid" class="form-control" value=""/>
				<!--<input type="hidden" name="tempbox" id="tempbox" value="<?php echo $_REQUEST['tempbox']; ?>" />-->
				</div>
			</div>

			<div class="col-md-4">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Bar&nbsp;Code</label>
				<div class="form-group">
				<input type="text" name="barCode"  id="barCode" class="form-control" value="" onblur="getVal();"/>
				<!--<select name="barCode" id="barCode" class="form-control" onchange="getVal();">
					<option value="">Select</option>
					<option value="9082">9082</option>
					<option value="9083">9083</option>
					<option value="9084">9084</option>
					<option value="9085">9085</option>
				</select>-->
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-success"  data-dismiss="modal" onclick="parent.$('#allotform').submit();" >Allot</button>
</div>
<script>
function getVal(){
	var vendor = $('#vendorid').val();
	var boxno = $('#boxnoid').val();
	var barCode = $('#barCode').val();
	//var tempbox = $('#tempbox').val();

	parent.$('#vendor').val(vendor);
	parent.$('#boxno').val(boxno);
	parent.$('#barCodeno').val(barCode);
	//parent.$('#tempboxid').val(tempbox);
}
</script>
<?php
}

/*
if($_REQUEST['action']=="selfassignment"){ ?>

<form class="form-horizontal" name="formaction" method="post" action="frmaction.php" target="actoinfrm">
	<div class="container-fluid">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-2">
				<div class="form-group">
					Assign All <input type="radio" autocomplete="off" name="selfAssign" value="all" onclick="funcSelfAssign(this.value);" checked />
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					Assign Single <input type="radio" autocomplete="off" name="selfAssign" value="single" onclick="funcSelfAssign(this.value);" />
				</div>
			</div>
			<div class="col-md-4 ackdiv" style="display:none;">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Product&nbsp;Type</label>
				<div class="form-group">
				<select name="productType" class="form-control">
					<option value="">Select</option>
					<option value="PAN">PAN</option>
					<option value="TAN">TAN</option>
				</select>
				</div>
			</div>
			<div class="col-md-4 ackdiv" style="display:none;">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Acknowledgement#</label>
				<div class="form-group">
					<input type="number" name="acknowledgementNo" id="acknowledgementNo" class="form-control" value="">
				</div>
			</div>
		</div>
	</div>
	<script>
	function funcSelfAssign(value){
		if(value=="single"){
			$('.ackdiv').show();
		}else{
			$('.ackdiv').hide();
		}
	}
	</script>
	<div class="modal-footer">
		<input type="hidden" name="action" value="selfassignment"  />
		<input type="hidden" name="userId" value="<?php echo $_SESSION['UID']; ?>"  />
		<button type="submit" class="btn btn-success">Save</button>
		<button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</form>
<script>
function reloadTable(){
	funcLoadTable();
}
</script>

<?php }
*/

if($_REQUEST['action']=="addintimationdate"){
?>

<div class="container">
	<div class="form-group">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-6">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Resolution&nbsp;Date</label>
				<div class="form-group">
				<input type="text" name="resolutionDate" id="resolutionDate" class="form-control datepicker" value="" placeholder="Resolution Date" />
				</div>
			</div>
			<div class="col-md-6">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Inward&nbsp;Date</label>
				<div class="form-group">
				<input type="text" name="inwardDate" id="inwardDate" class="form-control datepicker" value=""  placeholder="Inward Date" />
				</div>
			</div>


		</div>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-success"  data-dismiss="modal" onclick="funCheckDate();"  >Save</button>
</div>
<script>
function funCheckDate(){
	var resolutionDate = $('#resolutionDate').val();
	var inwardDate = $('#inwardDate').val();
	//parent.$('#resolutionDateqc').val(resolutionDate);
	//parent.$('#inwardDateqc').val(inwardDate);
	parent.$('#form1').contents().find('#resolutionDateqc').val(resolutionDate);
	parent.$('#form1').contents().find('#inwardDateqc').val(inwardDate);
	parent.$('#intimationBtn').click();
}

$( function() {
	$( "#resolutionDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		minDate:'-2D',
		maxDate: 0,
		onSelect: function(selected){
			var mindatenew = selected;
			mindatenew = mindatenew.substring(0,2);
			mindatenew = (Number(mindatenew-1))+"<?php echo date('-m-Y');?>";
			$('#inwardDate').datepicker("setDate",mindatenew);
		  }
	});
});

$( function() {
	//var today = new Date();
	//var tomorrow = new Date();
	$( "#inwardDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		minDate:'-3D',
		maxDate: 0
	});
});
</script>
<?php
}

if($_REQUEST['action']=="showNarration"){
$Narration = $_REQUEST['ndetail'];
?>
<div class="container">
	<div class="form-group">
		<p style="padding: 15px; font-size: 18px; color: blue;"><?php echo $Narration; ?></p>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<?php
}

if($_REQUEST['action']=="showJournalNarration"){
$Narration = $_REQUEST['ndetail'];
?>
<div class="container">
	<div class="form-group">
		<p style="padding: 15px; font-size: 18px; color: blue;"><?php echo $Narration; ?></p>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<?php
}


if($_REQUEST['action']=="showcomparision"){

	$urlOne = $serverurlapi."User1Entry/callAcknowData1.php?aid=".$_GET['aid'];
	$urlTwo = $serverurlapi."User1Entry/callAcknowData2.php?aid=".$_GET['aid'];

	$userJsonData1 = getCurlData($urlOne);
	$userArrData1 = json_decode($userJsonData1,true);

	$userJsonData2 = getCurlData($urlTwo);
	$userArrData2 = json_decode($userJsonData2,true);

?>
<div class="container" style="height: 400px; overflow:scroll;">
	<div class="row">
		<table class="table table-bordered">
			<thead style="background: #ebebeb;">
				<th>SR#</th>
				<th>Field</th>
				<th>User Entry 1</th>
				<th>User Entry 2</th>
			</thead>
			<tbody>
				<?php
				$sr=1;
				foreach($userArrData2['recordlist'] as $key=>$value){
					if(strtoupper($key)!='STAGE'){
					if(trim($userArrData1['recordlist'][$key])!=trim($value)){

				?>
					<tr>
						<td><?php echo $sr; ?></td>
						<td><?php echo strtoupper($key); ?></td>
						<td><?php echo $userArrData1['recordlist'][$key]; ?></td>
						<td><?php echo $value; ?></td>
					</tr>
				<?php

					$sr++;
					}
				}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<a href="dataentry.php?aid=<?php echo $_REQUEST['aid']; ?>&formType=<?php echo $_REQUEST['formType']; ?>" class="btn btn-success">Go To Entry Screen</a>
</div>
<?php
}

if($_REQUEST['action']=="schememaster"){


?>
	<form name="frmpost" action="frmaction.php" method="POST">
	<div class="container">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-6">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Name</label>
				<div class="form-group">
				<input type="text" name="SchemeName"  id="SchemeName" class="form-control" value=""/>
				</div>
			</div>
			<div class="col-md-6">
				<label class="control-label col-sm-2" for="email" style="padding: 0px;">Status</label>
				<div class="form-group">
				<select name="Status" id="Status" class="form-control">
					<option value="1">Active</option>
					<option value="0">In-Active</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<input type="hidden" name="CreatedBy" value="<?php echo $_SESSION['UID']; ?>"/>
		<input type="hidden" name="CreatedDate"  value="<?php echo date('Y-m-d'); ?>"/>
		<input type="hidden" name="action"  value="<?php echo $_REQUEST['action']; ?>"/>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-success">Save</button>
	</div>
	</form>
<?php
}

if($_REQUEST['action']=="schememasterdata"){
	$SchemeName = decode($_REQUEST['schemeName']);
	$eid = $_REQUEST['eid'];

	if($eid!=''){

		$jsonPost = '{
			"SchemeId":"'.decode($_REQUEST['sid']).'",
			"RateId":"'.$eid.'"
		  }';

		$url = $serverurlapi."General/listCommissionRate.php";
		$response = postCurlData($url,$jsonPost);
		//logger("RESPONCE RETURN FROM EDIT ID API: ". $response);
		$arrData = json_decode($response, true);
		$arrData = $arrData['commissionRateData'][0];
	}


?>
	<form name="frmpost" action="frmaction.php" method="POST">
	<div class="container">

			<div class="row" style="padding-top:10px;">
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">Charged&nbsp;Schedule</label>
					<div class="form-group">
					<input type="text" name="SchemeName"  id="SchemeName" class="form-control" value="<?php echo $SchemeName; ?>" readonly />
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">Type</label>
					<div class="form-group">
					<select name="Type" id="Type" class="form-control">
						<option value="">Select</option>
						<option value="1" <?php if($arrData['Type']=='1'){ echo 'selected'; } ?>>PAN Acceptance</option>
						<option value="2" <?php if($arrData['Type']=='2'){ echo 'selected'; } ?>>PAN Digitization</option>
						<option value="3" <?php if($arrData['Type']=='3'){ echo 'selected'; } ?>>PAN Incentive</option>
						<option value="4" <?php if($arrData['Type']=='4'){ echo 'selected'; } ?>>Tan Acceptance</option>
						<option value="5" <?php if($arrData['Type']=='5'){ echo 'selected'; } ?>>Tan Digitization</option>
						<option value="6" <?php if($arrData['Type']=='6'){ echo 'selected'; } ?>>Tan Incentive</option>
						<option value="7" <?php if($arrData['Type']=='7'){ echo 'selected'; } ?>>eTDS</option>
						<option value="8" <?php if($arrData['Type']=='8'){ echo 'selected'; } ?>>24G</option>
						<option value="9" <?php if($arrData['Type']=='9'){ echo 'selected'; } ?>>Mobile PAN</option>
					  </select>
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">From&nbsp;Date</label>
					<div class="form-group">
					<input type="text" name="FromDate"  id="FromDate" class="form-control datepicker" value="<?php if($arrData['FromDate']!=''){ echo date('d-m-Y',strtotime($arrData['FromDate']));  } ?>" readonly />
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">To&nbsp;Date</label>
					<div class="form-group">
					<input type="text" name="ToDate"  id="ToDate" class="form-control datepicker" value="<?php if($arrData['ToDate']!=''){  echo date('d-m-Y',strtotime($arrData['ToDate'])); } ?>" readonly />
					</div>
				</div>
			</div>
			<div class="row" style="padding-top:10px;">
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">App&nbsp;From</label>
					<div class="form-group">
					<input type="number" name="AppFrom"  id="AppFrom" class="form-control" value="<?php echo $arrData['AppFrom']; ?>"/>
					</div>
				</div>
				<div class="col-md-3">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">App&nbsp;To</label>
					<div class="form-group">
					<input type="number" name="AppTo"  id="AppTo" class="form-control" value="<?php echo $arrData['AppTo']; ?>"/>
					</div>
				</div>
				<div class="col-md-2">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">Status</label>
					<div class="form-group">
					<select name="Status" id="Status" class="form-control">
						<option value="1" <?php if($arrData['Status']=='1'){ echo 'selected'; } ?>>Active</option>
						<option value="0" <?php if($arrData['Status']=='0'){ echo 'selected'; } ?>>In-Active</option>
					  </select>
					</div>
				</div>
				<div class="col-md-2">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">Validity</label>
					<div class="form-group">
					<input type="number" name="Validity"  id="Validity" class="form-control" value="<?php echo $arrData['Days']; ?>"/>
					</div>
				</div>
				<div class="col-md-2">
					<label class="control-label col-sm-2" for="email" style="padding: 0px;">Amount</label>
					<div class="form-group">
						<input type="text" name="Amount"  id="Amount" class="form-control" value="<?php echo $arrData['Commission']; ?>"/>
					</div>
				</div>
			</div>

	</div>
	<div class="modal-footer">
		<input type="hidden" name="action"  value="<?php echo $_REQUEST['action']; ?>"/>
		<input type="hidden" name="sid"  value="<?php echo $_REQUEST['sid']; ?>"/>
		<input type="hidden" name="schemeName"  value="<?php echo $_REQUEST['schemeName']; ?>"/>
		<input type="hidden" name="editId"  value="<?php echo $arrData['RateId']; ?>"/>
		<input type="hidden" name="CreatedBy"  value="<?php echo $_SESSION['UID']; ?>"/>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-success">Save</button>
	</div>
	<script>
		$( function() {
			$( "#FromDate" ).datepicker({
				dateFormat: 'dd-mm-yy'
  			});
		});

		$( function() {
			$( ".datepicker" ).datepicker({
				dateFormat: 'dd-mm-yy',
				minDate:'0'
  			});
		});
	</script>
	</form>
<?php
}

?>
