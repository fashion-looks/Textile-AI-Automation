<?php 
// get url
include "inc.php";
include "logincheck.php";

$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;
logger($InfoMessage." URL for API - ".$url);
// $branchCode = '';
// if($_POST['action']=="searchaction")
// {
// $branchCode = $_POST['Branch'];
$searching = '{
    "BranchCode":"'.decode($_GET['bid']).'",
    "Type":"'.decode($_GET['type']).'",
    "BillPeriodId":"'.decode($_GET['bpi']).'"
}';

$url = $serverurlapi."General/billData.php";
logger($InfoMessage." URL for API - ".$url); 
logger($InfoMessage." Value for API - ".$searching); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
logger("RESPONSE RETURN FROM BILL DATA: ". $response);
$billData = json_decode($result, true);
curl_close($ch);
// }

// $loginType = strtoupper($_SESSION['Type']);
// if($loginType=="BRANCH"){
// 	$branchCodeNum = $_SESSION["BID"];
// }else{
// 	$branchCodeNum = $branchCode;
// }

// $jsonPost = '{
//     "branchCode":"'.$branchCodeNum.'"
// }';
// $url2 = $serverurlapi."General/branchBalanceAPI.php";
// $response = postCurlData($url2,$jsonPost);
// logger("Response return from branch Balance API: ". $response); 
// $balanceRes = json_decode($response);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Bill Detail</title>
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

      <div class="container-fluid" style="margin-top: 30px;">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
              <th>S.No</th>
              <th>Acknowedgement No</th>
              <th>Fees</th>
              <th>Branch Code</th>
              <th>Date Added</th>
              <th>Type</th>
              <th>Added By</th>
            </tr>
          </thead>
          <tbody id="tablesearch">
	<?php
    if(isset($billData['billData'])){                    
    $no=1;
    foreach($billData['billData'] as $resultList){
    ?>
            <tr class="uyt hgte">
              <td><?php echo $resultList['Number']; ?></td>
			  <td><?php echo $resultList['AckNumber'] ?></td>
        <td><?php echo $resultList['Fees']; ?></td>
        <td><?php echo $resultList['BranchCode']; ?></td>
        <td><?php echo date("d-m-Y",strtotime($resultList['DateAdded'])); ?></td>
        <td><?php echo $resultList['Type']; ?></td>
        <td><?php echo $resultList['AddedBy']; ?></td>
            </tr>
            <?php
    $no++;}}else{?>
    <tr class="uyt hgte">
<td colspan="8"><div align="center"><?php echo 'You Can Search...'; ?></div></td>    
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
