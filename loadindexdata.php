<?php
include "inc.php";
/*

	if($_SESSION["Type"]=="HOUSER"){
		$jsonPost = '{ "UserType": "'.$_SESSION["Type"].'", "UserId": "'.$_SESSION["UID"].'", "UserTypeId": "'.$_SESSION["branchId"].'", "AcknowledgementNo": "'.$_REQUEST['aid'].'", "uploadType": "'.$_REQUEST['uploadType'].'"}';

		$urlNew = $serverurlapi."Dashboards/DashboardAPI.php";
		$response = postCurlData($urlNew,$jsonPost);
		//logger("Response return from dashboard API: ". $response);
		$dashData = json_decode($response);

		$pan  = $dashData[0]->SummaryCounts[0]->SummaryCount;
		$tanCount  = $dashData[0]->SummaryCounts[0]->SummaryTanCount;

	}else{
		if($_REQUEST['action']=="branchdashboard"){
			$_SESSION['AID'] = $_REQUEST['aid'];
			$_SESSION['PROTYPE'] = $_REQUEST['productType'];
			$_SESSION['FORMTYPE'] = $_REQUEST['formType'];
			$_SESSION['SUBSTAGE'] = $_REQUEST['subStage'];
		}

		if($_SESSION["Type"]=="BRANCH"){
			if($_REQUEST['action']==''){
				$_SESSION['SUBSTAGE'] = "MU";
				$subStageFilter = $_SESSION['SUBSTAGE'];
			}else{
				$subStageFilter = $_SESSION['SUBSTAGE'];
			}
		}elseif($_SESSION["Type"]=="NSD"){
			if($_REQUEST['action']==''){
				$_SESSION['SUBSTAGE'] = "INS";
				$subStageFilter = $_SESSION['SUBSTAGE'];
			}else{
				$subStageFilter = $_SESSION['SUBSTAGE'];
			}
		}


		$jsonPost = '{ "UserType": "'.$_SESSION["Type"].'", "UserId": "'.$_SESSION["UID"].'", "UserTypeId": "'.$_SESSION["branchId"].'", "AcknowledgementNo": "'.$_SESSION['AID'].'", "productType": "'.$_SESSION['PROTYPE'].'", "formType": "'.$_SESSION['FORMTYPE'].'", "subStage": "'.$subStageFilter.'","Role": "'.$_SESSION['ROLE'].'"}';

		$urlNew = $serverurlapi."Dashboards/DashboardAPI.php";
		$response = postCurlData($urlNew,$jsonPost);
		//logger("Response return from dashboard API: ". $response);
		$dashData = json_decode($response);

		$pan  = $dashData[0]->SummaryCounts[0]->SummaryCount;
		$tanCount  = $dashData[0]->SummaryCounts[0]->SummaryTanCount;

	} */

?>
<!--

<section class="hk-sec-wrapper">
  <div class="grid-container">
    <div class="item1">
      <div class="grid">
        <h6>PAN</h6>
        <h2>
        	<?php echo ($pan!='') ? $pan : "0"; ?>

        </h2>
      </div>
    </div>
    <div class="item2">
      <div class="grid">
        <h6>TAN</h6>
        <h2><?php echo ($tanCount!='') ? $tanCount : "0"; ?></h2>
      </div>
    </div>
	<?php
	if($_SESSION["Type"]!="VENDOR"){
	?>
    <div class="item3">
      <div class="grid">
        <h6>TDS/TCS</h6>
        <h2><?php echo $countData->TdsCount; ?></h2>
      </div>
    </div>
    <div class="item4">
      <div class="grid">
        <h6>e-TDS/TCS</h6>
        <h2>0</h2>
      </div>
    </div>
    <div class="item5">
      <div class="grid">
        <h6>24G</h6>
        <h2><?php echo $countData->Count24g; ?></h2>
      </div>
    </div>
	<div class="item5">
      <div class="grid">
        <h6>Rejected</h6>
        <h2>10</h2>
      </div>
    </div>
	<?php } ?>
	<?php
	if($_SESSION["Type"]=="VENDOR"){
	$jsonPostVend = '{
					"vendorId":"'.$_SESSION['BID'].'",
					"userId":"'.$_SESSION['UID'].'"
				}';
	$vendorTotal = postCurlData($serverurlapi."General/vendorDetailAPI.php",$jsonPostVend);
	logger('TOTAL UNASSIN IN BUCKET FOR VENDOR: '.$vendorTotal);
	$vendorTotalUnassign = json_decode($vendorTotal);
	$AckNoPool  = $vendorTotalUnassign->AckNoPool;
	$VendorLimit  = $vendorTotalUnassign->VendorLimit;

	?>
	<div class="item1">
      <div class="grid">
        <h6>Unassigned</h6>
        <h2><?php echo ($AckNoPool!='') ? $AckNoPool : "0"; ?></h2>
      </div>
    </div>
	<?php } ?>

  </div>
</section>

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
#sel_4{
width: 250px !important;
}

#sel_6{
width: 150px !important;
}
#sel_7{
width: 100px !important;
}

.dataTables_wrapper {
padding: 3px !important;
}

</style>

<?php  if(isset($_SESSION['error'])!=''){ ?>
<div class="bs-example" id="messageDiv">

	<div class="alert alert-dismissible fade show" style="border: solid 2px; border-block-color: green; font-weight: 800; font-size: 17px; color: green;">
		  <?php echo $_SESSION['error'];unset($_SESSION['error']); ?>
		<button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
</div>
<?php } ?>


<?php if($_SESSION["Type"]=="HOUSER"){ ?>
<form method="GET">
<div class="container-fluid">
	<div class="row" style="padding: 5px; background: #e5e1e1;">
	  	<div class="col-md-3">
			<input type="number" name="aid" class="form-control" value="<?php echo $_REQUEST['aid']; ?>" placeholder="Enter Min. 5 Digit of Ack. Number" />
		</div>
		<div class="col-md-3">
			<select class="form-control" name="uploadType" id="uploadType">
				<option value="1" <?php if($_REQUEST['uploadType']=="1"){ echo 'selected'; } ?>>Express</option>
				<option value="2" <?php if($_REQUEST['uploadType']=="2"){ echo 'selected'; } ?>>Self</option>
			</select>
		</div>
		<div class="col-md-4">
			<input type="submit" class="btn btn-success" value="Search" />

		</div>
	</div>
</div>
</form>
<?php } ?>

<?php if($_SESSION["Type"]=="BRANCH" || $_SESSION["Type"]=="NSD"){ ?>
<form method="GET">
<div class="container-fluid" style="margin-top: 12px; margin-bottom: 12px;">
	<div class="row" style="padding: 5px; background: #e5e1e1;">
	  	<div class="col-md-3">
			<input type="number" name="aid" class="form-control" value="<?php echo $_REQUEST['aid']; ?>" placeholder="Enter Ack. Number" />
		</div>
		<div class="col-md-2">
			<select class="form-control" name="productType" id="productType">
				<option value="">All</option>
				<option value="PAN" <?php if($_REQUEST['productType']=="PAN"){ echo 'selected'; } ?> <?php if($_SESSION['PROTYPE']=="PAN"){ echo 'selected'; } ?>>PAN</option>
				<option value="TAN" <?php if($_REQUEST['productType']=="TAN"){ echo 'selected'; } ?> <?php if($_SESSION['PROTYPE']=="TAN"){ echo 'selected'; } ?>>TAN</option>
			</select>
		</div>
		<div class="col-md-2">
			<select class="form-control" name="formType" id="formType">
				<option value="">All</option>
				<option value="49A" <?php if($_REQUEST['formType']=="49A"){ echo 'selected'; } ?> <?php if($_SESSION['FORMTYPE']=="49A"){ echo 'selected'; } ?>>49A</option>
				<option value="49AA" <?php if($_REQUEST['formType']=="49AA"){ echo 'selected'; } ?> <?php if($_SESSION['FORMTYPE']=="49AA"){ echo 'selected'; } ?>>49AA</option>
				<option value="49B" <?php if($_REQUEST['formType']=="49B"){ echo 'selected'; } ?> <?php if($_SESSION['FORMTYPE']=="49B"){ echo 'selected'; } ?>>49B</option>
				<option value="CR" <?php if($_REQUEST['formType']=="CR"){ echo 'selected'; } ?> <?php if($_SESSION['FORMTYPE']=="CR"){ echo 'selected'; } ?>>CR</option>
			</select>
		</div>
		 <div class="col-md-2">
			<select class="form-control" name="subStage" id="subStage" required>

				<?php
				if($_SESSION["Type"]=="BRANCH"){
				?>
				<option value="MU" <?php  if($_REQUEST['subStage']=="MU"){ echo 'selected'; } ?> <?php  if($_SESSION['SUBSTAGE']=="MU"){ echo 'selected'; } ?>>Open</option>
				<option value="DU" <?php  if($_REQUEST['subStage']=="DU"){ echo 'selected'; } ?> <?php  if($_SESSION['SUBSTAGE']=="DU"){ echo 'selected'; } ?>>Document Uploaded</option>
				<option value="IP" <?php if($_REQUEST['subStage']=="IP"){ echo 'selected'; } ?> <?php if($_SESSION['SUBSTAGE']=="IP"){ echo 'selected'; } ?>>Cropped</option>
				<?php } ?>
				<?php
				if($_SESSION["Type"]=="NSD"){
				?>
				<option value="">Select</option>
				<option value="INS" <?php  if($_REQUEST['subStage']=="INS"){ echo 'selected'; } ?> <?php  if($_SESSION['SUBSTAGE']=="INS"){ echo 'selected'; } ?>>Batch Uploaded</option>
				<option value="ACC" <?php  if($_REQUEST['subStage']=="ACC"){ echo 'selected'; } ?> <?php  if($_SESSION['SUBSTAGE']=="ACC"){ echo 'selected'; } ?>>NSDL  Confirmation</option>
				<option value="ALC" <?php if($_REQUEST['subStage']=="ALC"){ echo 'selected'; } ?> <?php if($_SESSION['SUBSTAGE']=="ALC"){ echo 'selected'; } ?>>Allocated</option>
				<?php } ?>
			</select>
		</div>
		<div class="col-md-2">
			<input type="submit" class="btn btn-success" value="Search Data" />
			<input type="hidden" name="action" value="branchdashboard"  />
		</div>
	</div>
</div>
</form>
<?php } ?>


<?php if($_SESSION["Type"]=="QCP" || $_SESSION["Type"]=="QCF"){ ?>
<div class="container-fluid">
	<div class="row" style="padding: 5px; background: #e5e1e1;border: 1px solid #ccc;">
	<div class="col-md-12">
		<form class="form-horizontal" name="formaction" method="post" action="frmaction.php" target="actoinfrm">
			<input type="hidden" name="selfAssign" value="all"  />
			<input type="hidden" name="action" value="selfassignment"  />
			<input type="hidden" name="userId" value="<?php echo $_SESSION['UID']; ?>"  />
			<div class="row">
				<div class="col-md-2">
					<div class="row" style="margin-left: 0px !important;">
						<div style="display: grid; grid-template-columns: 2fr 1fr; padding: 4px; border: 2px solid #00acf0; background: #c9e4ff;">
							<div class="form-check ">
							  <input type="radio" class="form-check-input" name="productType" value="PAN" onclick="funcShowFilter(this.value);" checked >
							  <label class="form-check-label" for="inlineRadio3">PAN</label>
							</div>
							<div class="form-check ">
							  <input type="radio" class="form-check-input" name="productType" value="TAN" onclick="funcShowFilter(this.value);" >
							  <label class="form-check-label" for="inlineRadio3">TAN</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="row">
						<div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; padding: 4px; border: 2px solid #00acf0; background: #c9e4ff;">
							<div class="form-check">
								  <input type="radio" class="form-check-input 49A" name="formType" value="49A" required>
								  <label class="form-check-label 49A" for="inlineRadio3">PAN New</label>

								  <input type="radio" class="form-check-input 49B" name="formType" value="49B">
							  	  <label class="form-check-label 49B" for="inlineRadio3">TAN New</label>
							</div>

							<div class="form-check 49A">
							  <input type="radio" class="form-check-input" name="formType" value="49AA">
							  <label class="form-check-label" for="inlineRadio3">NRI</label>
							</div>


							<div class="form-check">
							  <input type="radio" class="form-check-input CR" name="formType" value="CR">
							  <label class="form-check-label" for="inlineRadio3"><span id="pid"></span>CR</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="row">
						<div class="col-md-4">
							<div class="form-check">
								  <button type="submit" class="btn btn-primary" style="font-size: 13px; font-weight: 700;" onClick="funcLoading();">Self Assign</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
<script>
function funcShowFilter(pType){
	if(pType=="PAN"){
		$(".49A").css("display","block");
		$(".49B").css("display","none");
		$("#pid").text("PAN-");
	}else{
		$(".49B").css("display","block");
		$(".49A").css("display","none");
		$("#pid").text("TAN-");
	}
}
funcShowFilter("PAN");
</script>
<?php } ?>
-->
<!--<div id="tabledata" style="padding: 10px;">-->

<!--
<?php if($_SESSION["Type"]=="VENDOR"){ ?>
<form class="form-horizontal" name="formaction" method="post" action="frmaction.php" target="actoinfrm">
	<input type="hidden" name="vendorCode" value="<?php echo $_SESSION['BID']; ?>"  />
	<input type="hidden" name="action" value="selfassignmentvendor"  />
	<input type="hidden" name="userId" value="<?php echo $_SESSION['UID']; ?>"  />
	<div style="float:right;">
		<button type="submit" class="btn btn-primary" style="font-size: 13px; font-weight: 700;" onClick="funcLoading();">Self Assign</button>
	</div>
</form>
<?php } ?>
<table id="tableID" class="table table-bordered table-responsive" style="width:100%;">
  <thead>
   <tr class="headline" style="background: #757575;">
   	  <th class="thCls table-heading">Color</th>
      <th class="thCls table-heading">Acknowledgement&nbsp;No.</th>
	  <th class="thCls table-heading">Product&nbsp;Type</th>
      <th class="thCls table-heading" >Form&nbsp;Type</th>
      <th class="thCls table-heading">Category</th>
      <th class="thCls table-heading">Stage</th>
      <th class="thCls table-heading">Sub Stage</th>
      <th class="thCls table-heading">Ack.&nbsp;Date</th>

	 <?php if($_SESSION['Type']=="QCP" || $_SESSION['Type']=="QCF" || $_SESSION['Type']=="QCF" || $_SESSION['Type']=="HOUSER"){ ?> <th class="thCls table-heading">Assign</th><?php } ?>
	 <?php if($_SESSION["Type"]=="HOUSER"){ ?><th class="thCls table-heading"></th><?php } ?>
    </tr>
	<tr style="background: #5ea923;">

      <th align="center">
      	<select class="filterCls" id="color" autocomplete="off" >
        <option value="">Select</option>
			  <option value="#2eb82e"><span style="color:#2eb82e;">#2eb82e</span></option>
		  	<option value="#ffa31a"><span style="color:#ffa31a;">#ffa31a</span></option>
			  <option value="#ff1a1a"><span style="color:#ff1a1a;">#ff1a1a</span></option>
        </select></th>
      <th></th>
	  <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
	  <?php if($_SESSION['Type']=="QCP" || $_SESSION['Type']=="QCF" || $_SESSION['Type']=="HOUSER"){ ?><th></th><?php } ?>
	  <?php if($_SESSION["Type"]=="HOUSER"){ ?><th></th><?php } ?>
    </tr>
  </thead>
  <tbody id="searchTable">
   <?php


if($dashData[0]->Status=='0')
{
$color1='';
$color2='';
$UserType = $_SESSION["Type"];
$no=1;
foreach($dashData[0]->DataTable as $list)
{


	if(strtoupper($list->ProductType)=='PAN'){
		  if($UserType =="QCF" || $UserType =="HOUSER" || $UserType =="VENDOR" || ($UserType =="BRANCH" && $_SESSION['ROLE']=='1'))
		  {

			if( strtoupper($list->SubStageId)=="0")
			{
			  $stagurl = "filesubmit.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}elseif($list->SubStageId=="1")
			{
			  $stagurl = strtoupper(trim($list->ApplicantCategory))=="INDIVIDUAL"?"cropingstage.php?aid=" :  "dataentry.php?aid="  ;
			   $stagurl  .= $list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}elseif($list->SubStageId=="2")
			{
			  $stagurl = "dataentry.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}
		  }
		  else if($UserType =="BRANCH" && $_SESSION['ROLE']=='2')
		  {
		  	$stagurl = "dataentry.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
		  }
		  else if($UserType =="QCP" || $UserType =="BCP")
		  {
			if($list->SubStageId=="1")
			{
			  $stagurl = $list->ApplicationCategory=="INDIVIDUAL"?"cropingstage.php?aid=" :  "qc_pdf.php?aid="  ;
			   $stagurl  .= $list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}else{
				$stagurl = "qc_pdf.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}
		  }else if($UserType =="NSD")
		  {
			if($list->SubStageId=="1")
			{
			  $stagurl = "#";

			}else{
				$stagurl = "#";
			}
		  }else{
				echo 'Invalid User Type';
				exit();
		  }
	}elseif(strtoupper($list->ProductType)=='TAN'){
		  if($UserType =="QCF" || $UserType =="HOUSER" || $UserType =="VENDOR" || ($UserType =="BRANCH" && $_SESSION['ROLE']=='1'))
		  {

			if( strtoupper($list->SubStageId)=="0")
			{
			  $stagurl = "filesubmittan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}elseif($list->SubStageId=="1")
			{
			  $stagurl = "dataentrytan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}elseif($list->SubStageId=="2")
			{
			  $stagurl = "dataentrytan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}
		  }
		  else if($UserType =="BRANCH" && $_SESSION['ROLE']=='2')
		  {
		  	$stagurl = "dataentrytan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
		  }
		  else if($UserType =="QCP" || $UserType =="BCP")
		  {
			if($list->SubStageId=="1")
			{
				$stagurl = "qc_pdftan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}else{
				$stagurl = "qc_pdftan.php?aid=".$list->Acknowledgement."&formType=".strtoupper($list->FormType);
			}
		  }else if($UserType =="NSD")
		  {
			if($list->SubStageId=="1")
			{
			  $stagurl = "#";

			}else{
				$stagurl = "#";
			}
		  }else{
				echo 'Invalid User Type';
				exit();
		  }
	}else{
		echo 'Invalid Product Type';
		exit();
	}

	if($list->ApplicationAgeing<=2 || $list->ApplicationAgeing==''){
		$color='#2eb82e';

	}else if($list->ApplicationAgeing>=3 && $list->ApplicationAgeing<=6 ){
		$color='#ffa31a';

	}else if($list->ApplicationAgeing>6){
		$color='#ff1a1a';
	}

	if($list->IsCancel==0){
?>
    <tr class="cls_<?php echo $list->Acknowledgement; ?>" style=" <?php if($UserType =="QCP" || $UserType =="QCF" || $_SESSION["Type"]=="HOUSER"){ if($list->AssignTo!=$_SESSION['UID']){  ?> background: #ccc;  <?php } } ?>">
	  <td class="table-data" ><div style="background-color:<?php echo $color; ?>; padding: 0px; color:<?php echo $color; ?>; width:25px;height: 20px;font-size: 1px;"><?php echo $color; ?><?php echo $UserType; ?> </div></td>
      <td class="deta" style="cursor:pointer;"><a data-toggle="tooltip" data-placement="top" title="<?php echo $list->AssignToName; ?>" <?php if($UserType =="QCP" || $UserType =="QCF" || $_SESSION["Type"]=="HOUSER"){ if($list->AssignTo==$_SESSION['UID']){  ?> href="<?php echo $stagurl; ?>"  <?php }else{ ?>onclick="submitSelfForm('<?php echo $list->Acknowledgement; ?>','<?php echo strtoupper($list->ProductType); ?>','<?php echo $stagurl; ?>','<?php echo $list->AssignTo; ?>');" <?php } }else{ ?>href="<?php echo $stagurl; ?>"   <?php } ?> target="blank">


	  <?php echo $list->Acknowledgement; ?></a>
	  </td>
	   <td  class="table-data"><?php echo strtoupper($list->ProductType); ?></td>
      <td class="table-data"><?php echo strtoupper($list->FormType); ?></td>
      <td class="table-data"><?php echo strtoupper($list->ApplicantCategory);  ?></td>
      <?php if($UserType=="NSD"){ ?>
	  <td class="table-data"><?php echo $list->ProcessPoint; ?></td>
      <td class="table-data"><?php if($list->CurrentStage=="NSDL Allocated"){ echo str_replace("NSDL",strtoupper($list->ProductType),$list->CurrentStage); }else{ echo $list->CurrentStage; }  ?></td>
      <?php }else{ ?>
	  <td class="table-data"><?php echo $list->CurrentStage; ?></td>
      <td class="table-data"><?php echo $list->SubStage; ?></td>
	  <?php } ?>
      <td class="table-data"><div style="width:95px;"><?php echo $list->AcknowDate; ?></div></td>
	  <?php if($_SESSION["Type"]=="QCP" || $_SESSION["Type"]=="QCF" || $_SESSION["Type"]=="HOUSER"){ ?>
	  <td class="table-data" style="cursor:pointer;font-weight: 600;" >
	    <?php
	    if($list->AssignTo==''){
	    	$assinValue = 'Queued';
		}elseif($list->AssignTo==$_SESSION['UID']){
			$assinValue = 'Me';
		}else{
			$assinValue = 'Other';
		}
	    ?>
		<?php echo $assinValue; ?>
		</td>
		<?php } ?>
		<?php if($_SESSION["Type"]=="HOUSER"){ ?>
		<td class="table-data"><a id="cancelack_<?php echo $list->Acknowledgement; ?>" onclick="funCancel('<?php echo $list->Acknowledgement; ?>');" style=" cursor: pointer; padding: 5px; background: red; color: white; font-weight: 500; border: 1px solid #f16d6d; ">Cancel</a>
	</td>
			<?php } ?>
    </tr>
    <?php
	}
	$no++; }   }else{
	echo 'no data found';
	}
	?>
  </tbody>
</table>

</div>
<script>
	function funCancel(ackNo){
		var confmessage = confirm('Are you sure you want to cancel this AckNo: '+ackNo);
		if(confmessage==true){
			$('#cancelack_'+ackNo).text('Loading...');
			$('#cancelack_'+ackNo).load('frmaction.php?action=cancelack&ack='+ackNo);

		}
	}
</script>

<div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: #000000c7; z-index: 9999; display:none;" id="blkbox">
	<div style="padding:20px; background-color:#FFFFFF; margin:auto; width:300px; margin-top:10%; text-align:center; border-radius: 10px;color: green;"><img src="img/Spin2.gif" width="100px;"><br>Loading... Please wait</div>
</div>

<form name="formaction_" method="post" action="frmaction.php" target="actoinfrm" style="display:none;">
	<input type="hidden" name="selfAssign" id="selfAssign" value="single"  />
	<input type="hidden" name="redirecturl" id="redirecturl" value=""  />
	<input type="hidden" name="action" id="action" value="selfassignment"  />
	<input type="hidden" name="userId" id="userId" value="<?php echo $_SESSION['UID']; ?>"  />
	<input type="hidden" name="UserType" id="UserType" value="<?php echo strtoupper($_SESSION['Type']); ?>"  />
	<input type="hidden" name="acknowledgementNo" id="acknowledgementNo" value=""  />
	<input type="hidden" name="productType" id="productType" value=""  />
	<button type="submit" id="btnSubmit" class="btn btn-default" onClick="funcLoading();" style="font-size:12px; padding:5px; border:1px solid #5ea923; width:57px;"><?php echo $assinValue; ?></button>
</form>

<script>
function submitSelfForm(ackNo,productType,stage,assignTo){
	$('#redirecturl').val(stage);
	$('#acknowledgementNo').val(ackNo);
	$('#productType').val(productType);
	var userId = '<?php echo $_SESSION['UID']; ?>';

	if(assignTo!=''){
		var conMess = confirm("Are you sure you want to assign this to you?");
		if(conMess==true){
			$('#btnSubmit').click();
		}
	}else{
		$('#btnSubmit').click();
	}



}

$(document).ready(function(){
	$('#color option').each(function () {
		var color = $(this).text();
		$(this).closest("option").css({"background-color":color,"color":color});
	})
});

$(document).ready(function(){
	$("#color").on("change", function() {
		var value = $(this).val().toLowerCase();
		$("#searchTable tr").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
});
</script>

<script>

// Paging and other information are
// disabled if required, set to true
	var myTable = $("#tableID").DataTable({
	  paging: true,
	  searching: true,
	  info: true,
	  stateSave: true,
	  orderCellsTop: true,
	  oLanguage: {
      		"sSearch": "Filter records:"
    	},
	  initComplete: function () {

				<?php if($_SESSION["Type"]=="QCP" || $_SESSION["Type"]=="QCF" || $_SESSION["Type"]=="HOUSER"){ ?>
					const arrFilter = [2,3,4,5,6,8];
				<?php }else{ ?>
					const arrFilter = [2,3,4,5,6];
				<?php } ?>
	   			this.api().columns(arrFilter).every( function (i) {
				var selcolID = "sel_"+i;
				var column = this;

				var select = $('<select class="filterCls table-select" id="'+selcolID+'"><option value="">All</option></select>')
				   .appendTo( $("#tableID thead tr:eq(1) th").eq(column.index()).empty() )
						.on( 'change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);
						column
							.search( val ? '^'+val+'$' : '', true, false )
							.draw();
					} );

				column.data().unique().sort().each( function ( d, j ) {
						var filter = column.state().columns[i].search.search;
						var newStr = filter.replace('^', '');
						var filterStr = newStr.replace('$', '');
						filterStr =  filterStr.replace(/\\/g, '')
						//alert(d+'++++++++++'+filter);
						var selected = (d === filterStr) ? 'selected' : '';
						select.append( '<option value="'+d+'" title="'+d+'" ' + selected + '>'+d.substr(0,30)+'</option>' );
				} );


			} );

		}


	});

function funcLoading(){
$('form').submit(function (e) {
 e.preventDefault;
 $('#blkbox').show();
 if (e.result == true) {
   $('#blkbox').show();
 }
});
	//
}


/*function funcCheckSubs(ackno,assignto,userid,productType){
	$('#loadtable').load('frmaction.php?action=selfassignment&selfAssign=single&acknowledgementNo='+ackno+'&userId='+userid+'&productType='+productType);
	//$('#btn_1_'+ackno).text('Loading..');


}*/
</script>
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
--->
