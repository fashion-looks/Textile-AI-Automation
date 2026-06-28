<?php 
include "inc.php";
include "logincheck.php";

$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;
logger($InfoMessage." URL for API - ".$url);
$branchCode = '';
if($_POST['action']=="searchaction")
{
$jsonData = '{
    "commissionPeriod":"'.$_POST['commissionPeriod'].'"
}';

$url = $serverurlapi."General/commissionGenerationAPI.php";
logger($InfoMessage." URL for API - ".$url); 
logger($InfoMessage." Value for API - ".$searching); 
$resultData = postCurlData($url,$jsonData);
$commissionReportData = json_decode($resultData);
}

if($_POST['action']=="generateaction")
{
$jsonData = '{
    "commissionPeriod":"'.$_POST['commissionPeriod'].'",
    "UserId":"'.$_SESSION['UID'].'",
	  "ip":"'.$_SERVER["REMOTE_ADDR"].'"
}';

$url = $serverurlapi."General/addCommissionVoucherAPI.php";
logger($InfoMessage." URL for API - ".$url); 
logger($InfoMessage." Value for API - ".$searching); 
$resultData = postCurlData($url,$jsonData);
// $commissionReportData = json_decode($resultData);
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
<title>Commission Generation</title>
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
          <div class="col-md-4">
          <div >
          <h6 style="font-weight: initial;">Commission Period</h6>
           <select name="commissionPeriod" id="commissionPeriod" class="form-control">
            <option value="">Select</option>
            <?php
      $result = postCurlData($serverurlapi."General/commissionInformationAPI.php","");
      $commissionData = json_decode($result, true);
      if(isset($commissionData['status'])=='true'){
      if(isset($commissionData['commissionPeriod'])){                    
      $no=1;
      foreach($commissionData['commissionPeriod'] as $resultList){
      ?>
            <option value="<?php echo $resultList['Id']; ?>"  <?php if($_POST['commissionPeriod'] == $resultList['Id']){ echo "selected"; } if($resultList['Status'] == 1){ echo "disabled"; } ?>><?php echo date('d/m/Y',strtotime($resultList['FromDate']))." - ".date('d/m/Y',strtotime($resultList['ToDate'])); ?></option>
      <?php } } } ?>
            </select>

           </select>
          </div>
        </div>
        
        <div class="col-md-1">
          <div>
            <h6>&nbsp;</h6>
          <input type="hidden" id="action" name="action" value="" />
          <div style="display:grid;grid-template-columns: auto auto;grid-gap: 10px;">
          <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('searchaction');" value="Display" />
          <input type="button" name="Generate" id="Generate" class="btn btn-success" onClick="searchFunc('generateaction');" value="Generate Commission" />
          </div>
        </div>
        </div>
		
        </div>
      </form>
	 
    </div>
     <script>
 function searchFunc(data){
  $('#action').val(data);
  $('form#exportfrm').submit();
 }

var check= $('#commissionPeriod').val();

if(check != ""){
$('#Generate').show();
}else{
$('#Generate').hide();
}
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
      <div class="container-fluid" style="overflow-x:scroll;">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
              <th>S.No</th>
              <th>BranchCode</th>
			        <th>BranchName</th>
              <th>NoOfAppPAN</th>
              <th>PAN</th>
              <th>NoOfAppTAN</th>
              <th>TAN</th>
              <th>NoOfAppReturn</th>
              <th>eReturn</th>
              <th>NoOfApp24G</th>
              <th>24G</th>
              <th>NoOfAppMobilePAN</th>
              <th>MobilePAN</th>
            </tr>
          </thead>
          <tbody id="tablesearch">
	<?php
    if(isset($commissionReportData->Status) == 'true'){
    if(isset($commissionReportData->TotalData)){                    
    $no=1;
    foreach($commissionReportData->TotalData as $resultList){
    ?>
              <tr class="uyt hgte">
              <td><?php echo $no; ?></td>
              <td><?php echo $resultList->BranchCode; ?></td>
              <td><?php echo $resultList->BranchName; ?></td>
              <td><?php echo $resultList->PanCount; ?></td>
              <td><?php echo $resultList->PanTotal; ?></td>
              <td><?php echo $resultList->TanCount; ?></td>
              <td><?php echo $resultList->TanTotal; ?></td>
              <td><?php echo $resultList->EReturnCount; ?></td>
              <td><?php echo $resultList->EReturnTotal; ?></td>
              <td><?php echo $resultList->G24Count; ?></td>
              <td><?php echo $resultList->G24Total; ?></td>
              <td><?php echo $resultList->MPANCount; ?></td>
              <td><?php echo $resultList->MPANTotal; ?></td>
            </tr>
             <?php $no++; } } }
   else{ ?>
    <tr class="uyt hgte">
<td colspan="13"><div align="center"><?php echo 'You Can Search...'; ?></div></td>    
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
