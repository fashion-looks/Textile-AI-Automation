<?php 
// get url 
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;

// $loginType = strtoupper($_SESSION['Type']);

// if($loginType=="BRANCH"){
// 	$branchCode = $_SESSION["BID"];
// }else{
// 	$branchCode = '';
// }



if($_GET['action']=="searchaction"){

$name = trim($_GET['name']);
$accountGroup = trim($_GET['accountGroup']);
  
$jsonData = '{
  	"name":"'.$name.'",
    "accountGroup":"'.$accountGroup.'"
}';

$url = $serverurlapi."General/accountSubGroupAPI.php";
$resultData = postCurlData($url,$jsonData);
logger('Response return from account Sub Group API: '.$resultData);
$accountData = json_decode($resultData);

}






?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>List Account Group</title>
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
          <h6 style="font-weight: initial;">Sub Group Name</h6>
           <input type="text" name="name" class="inp-w ui-select" value="<?php echo $_GET['name']; ?>"  autocomplete="off" />
          </div>
        </div>
         <div class="col-md-3">
          <div >
          <h6 style="font-weight: initial;">Group Type</h6>
           <select class="inp-w ui-select" name="accountGroup" >
				<option value="">All</option>
				<option value="1" <?php if(trim($_GET['accountGroup'])=='1'){?>selected="selected"<?php } ?>>Assets</option>
            </select>
          </div>
        </div>
		<div class="col-md-2">
          <div>
            <h6>&nbsp;</h6>
          <input type="hidden" id="action" name="action" value="searchaction" />
          <input type="submit" name="Search" class="inp-w ui-select btn btn-success" value="Search" />
          </div>
        </div>
		
		<div class="col-md-3">
          <div>
            <h6>&nbsp;</h6>
           <div class=""> <a href="addAccountSubGroup.php" class="btn btn-default browsebutton pd-btns">Add Sub Account Group </a></div>
          </div>
        </div>
		
		</div>
      </form>
	  
	 
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
			  <th>Sub Group Name</th>
			  <th>Group Type</th>
			</tr>
          </thead>
          <tbody id="tablesearch">
            <?php
    if(isset($accountData->status)=='true'){
    if(isset($accountData->AccountSubGroupData)){                    
    $no=1;
    foreach($accountData->AccountSubGroupData as $resultList){

    ?>
            <tr class="uyt hgte">
			 
			  <td><?php echo $resultList->Name; ?></td>
			  <td><?php echo $resultList->GroupName; ?></td>
			  
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
