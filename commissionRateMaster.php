 <?php 
// get url
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;
logger($InfoMessage." URL for API - ".$url); 
if(isset($_POST['Search']))
{
  	$searching = '{
    "List": [
        {
            "Commission": "'.$_POST['Name'].'",
            "FormType": "'.$_POST['formType'].'",
            "Status": "'.$_POST['Status'].'",
            "commissionType": "'.$_POST['commissionType'].'"
        }
    ]
}';
	$url = $serverurlapi."General/commissionRateAPI.php";
	$response = postCurlData($url,$searching);
	logger("Response return from Audit Trail API SEARCH: ". $response); 
	$regionData = json_decode($response);
}else{
	$searching = "";
	$url = $serverurlapi."General/commissionRateAPI.php";
	$response = postCurlData($url,$searching);
	logger("Response return from Commison RATE list API: ". $response); 
	$regionData = json_decode($response);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Commission Rate Master</title>
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
<div class="hk-wrapper hk-vertical-nav">
  <!-- Top Navbar -->
  <?php include 'header.php'; ?>
  <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  <div class="hk-pg-wrapper"  style="">
    <!-- <div style="background:transparent;">

</div> -->
    
    <div class="container-fluid">
    <form action="" method="post" />
      <div class="row gy-bvc" style="">
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Commission Rate</h6>
            <input class="inp-w" type="text" placeholder="Search Via Commissiom Rate..." autocomplete="off" name="Name" value="<?php echo $_POST['Name']; ?>" id="Name">
          </div>
        </div>
		<div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Commission Type</h6>
            <select class="inp-w ui-select" name="commissionType">
              <option value="">Select</option>
              <option value="A" <?php if($_POST['commissionType']=='A'){?>selected="selected"<?php } ?>>Acceptance</option>
              <option value="D" <?php if($_POST['commissionType']=='D'){?>selected="selected"<?php } ?>>Digitization</option>
			  <option value="I" <?php if($_POST['commissionType']=='I'){?>selected="selected"<?php } ?>>Incentive</option>
            </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Form&nbsp;Type</h6>
            <select class="inp-w ui-select" name="formType">
              <option value="">Select</option>
              <option value="49A" <?php if($_POST['formType']=='49A'){?>selected="selected"<?php } ?>>49A</option>
              <option value="49AA" <?php if($_POST['formType']=='49AA'){?>selected="selected"<?php } ?>>49AA</option>
			  <option value="49B" <?php if($_POST['formType']=='49B'){?>selected="selected"<?php } ?>>49B</option>
			  <option value="CR PAN" <?php if($_POST['formType']=='CR PAN'){?>selected="selected"<?php } ?>>CR PAN</option>
			  <option value="CR TAN" <?php if($_POST['formType']=='CR TAN'){?>selected="selected"<?php } ?>>CR TAN</option>
            </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Status</h6>
            <select class="inp-w ui-select" name="Status" value="<?php echo $_POST['Status']; ?>">
              <option value="">Select</option>
              <option value="1" <?php if($_POST['Status']=='1'){?>selected="selected"<?php } ?>>Active</option>
              <option value="0" <?php if($_POST['Status']=='0'){?>selected="selected"<?php } ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="search-input-grid">
          <h6>&nbsp;</h6>
         <div class="search-button">
              <input type="submit" name="Search" class="btn btn-default browsebutton pd-button" value="Search" />
              <button type="reset" class="btn btn-default browsebutton pd-button">Reset</button>
          </div>
        </div>
        </div>
      </div>
    </form>
      <div class="row gy-bvc nn-mb">
        <div class="col-md-12">
          <div class="row lk-kl">
            <div class=""> <a href="addCommissionRate.php">
              <button type="button" class="btn btn-default browsebutton pd-btns"> <i class="fa-plus fa">&nbsp;</i>Add</button>
              </a></div>
          </div>
        </div>
      </div>
      </section>
      <div class="container-fluid">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
              <th>S.No</th>
              <th>Commission Rate</th>
			  <th>Commission Type</th>
              <th>Form&nbsp;Type</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="tablesearch">
    <?php
    $no=1;
    foreach($regionData->List as $resultList){
    ?>
		<tr class="uyt hgte">
		  <td><?php echo $no; ?></td>
		  <td><?php echo $resultList->Commission; ?></td>
		  <td><?php if($resultList->commissionType=='A'){echo 'Acceptance'; }elseif($resultList->commissionType=='D'){ echo 'Digitization'; }elseif($resultList->commissionType=='I'){ echo 'Incentive'; } ?></td>
		  <td><?php echo $resultList->FormType; ?></td>
		  <td><?php if($resultList->Status==1){echo 'Active';} else{ echo 'Inactive'; } ?></td>
		  <td><div class="gvre"> <a href="addCommissionRate.php?editId=<?php echo encode($resultList->Id); ?>" class="btn btn-default branchbtn" style="width: 100%;">Edit</a></div></td>
		</tr>
     <?php
    	$no++;
	}
	if($no==1){
	?>
    <tr class="uyt hgte">
<td colspan="5"><div align="center"><?php echo 'No Result Found...'; ?></div></td>    
    </tr>
    <?php } ?>
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
