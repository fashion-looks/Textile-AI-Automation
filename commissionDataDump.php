<?php 
// get url
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;


if($_POST['action']=="searchaction"){

$fromDate = trim($_POST['fromDate']);
$toDate = trim($_POST['toDate']);
  
$searching = '{
  "fromDate":"'.date('Y-m-d',strtotime($fromDate)).'",
    "toDate":"'.date('Y-m-d',strtotime($toDate)).'"
}';
$url = "".$serverurlapi."HOOperation/mailDataAPI.php";
logger($InfoMessage." URL for API - ".$searching); 
logger($InfoMessage." URL for API - ".$url); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
$regionData = json_decode($result, true);
curl_close($ch);

}

if($_POST['action']=="exportaction"){

$fromDate = trim($_POST['fromDate']);
$toDate = trim($_POST['toDate']);
  
$searching = '{
  "fromDate":"'.date('Y-m-d',strtotime($fromDate)).'",
    "toDate":"'.date('Y-m-d',strtotime($toDate)).'"
}';
$url = "".$serverurlapi."HOOperation/mailDataAPI.php";
logger($InfoMessage." URL for export API - ".$searching); 
logger($InfoMessage." URL for export API - ".$url);  

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
$regionData = json_decode($result, true);
curl_close($ch);

logger($InfoMessage." URL for export API - ".$result); 

// Excel file name for download 
$fileReport = "RejIntimation_From_" . $fromDate . "_".$toDate.".csv"; 
$delimiter = ",";

 // Create a file pointer 
    $f = fopen('php://memory', 'w'); 

// Column names 
$fields = array('Acknowledgement No', 'Email Id', 'Contact Details', 'Acknowledgement Date', 'Rejection DateTime', 'Mail Sent DateTime', 'SMS Sent DateTime', 'Mail Status','SMS Status'); 
 fputcsv($f, $fields, $delimiter); 
 
  if(isset($regionData['status'])=='true'){
    if(isset($regionData['MailData'])){                    
        foreach($regionData['MailData'] as $resultList){
       $lineData = array($resultList['ACKNO'], $resultList['EmailId'], $resultList['Telephone'], $resultList['AckDate'],$resultList['rejDate'], substr($resultList['maildateAdded'],0,19), substr($resultList['smsdateAdded'],0,19),$resultList['mailStatus'],$resultList['smsStatus']);

       fputcsv($f, $lineData, $delimiter); 
      }
    }
  }
  // Move back to beginning of file 
    fseek($f, 0); 
  
 
 // Set headers to download file rather than displayed 
    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename="' . $fileReport . '";'); 
     
    //output all remaining data on a file pointer 
    fpassthru($f); 
    
exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>PAN Dashboard</title>
<meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
<?php include 'links.php'; ?>
<script>
$(document).ready(function(){
    $('#datatable').DataTable({
       "lengthMenu": [50, 100, 250, 500, "All"],
       "pageLength": 100

    });
} );
</script>
<!-- Favicon -->
</head>
<body>
<!-- HK Wrapper -->
<div class="hk-wrapper hk-vertical-nav hk-nav-toggle">
  <!-- Top Navbar -->
  <?php include 'header.php'; ?>
  <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  <div class="hk-pg-wrapper"  style="background-image: url(../html/dist/img/Religare-Dashboard-BG.JPG);">
    <div class="container-fluid">
      <form action="" method="POST" autocomplete="nope" id="exportfrm" />
        <div class="row gy-bvc">
        <div class="col-md-3">
          <div class="flx">
          <h6 style="font-weight: initial;">From&nbsp;Date</h6>
          <input type="text" name="fromDate" class="form-control datepicker" value="<?php echo $_POST['fromDate']; ?>" required autocomplete="off" />
          </div>
        </div>
        <div class="col-md-3">
          <div class="flx">
          <h6 style="font-weight: initial;">To&nbsp;Date</h6>
           <input type="text" name="toDate" class="form-control datepicker" value="<?php echo $_POST['toDate']; ?>" required autocomplete="off" />
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="flx">
          <input type="hidden" id="action" name="action" value="" />
          <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('searchaction');" value="Search" />
          <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('exportaction');" value="Export Data" />
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
 </script> 
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">

      <div class="container-fluid" style="margin-top:20px">
        <table id="datatable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr class="vcx-i hgt">
              <th>S.No</th>
              <th>Ack&nbsp;No</th>
              <th>Email Id</th>
              <th>Contact Details</th>
              <th>Ack&nbsp;Date</th>
              <th>Rej. DateTime</th>
              <th>Mail Sent DateTime</th>
              <th>SMS Sent DateTime</th>
              <th>Mail Status</th>
              <th>SMS Status</th>
              <th>Download Rejection Letter</th>
            </tr>
          </thead>
          <tbody id="tablesearch">
            <?php
    if(isset($regionData['status'])=='true'){
    if(isset($regionData['MailData'])){                    
    $no=1;
    foreach($regionData['MailData'] as $resultList){


    ?>
            <tr class="uyt hgte">
              <td><?php echo $no; ?></td>
              <td><?php echo $resultList['ACKNO']; ?></td>
              <td><?php echo $resultList['EmailId']; ?></td>
              <td><?php echo $resultList['Telephone'] ?></td>
              <td><?php echo $resultList['AckDate']; ?></td>
              <td><?php echo $resultList['rejDate']; ?></td>
              <td><?php echo substr($resultList['maildateAdded'],0,19); ?></td>
              <td><?php echo substr($resultList['smsdateAdded'],0,19); ?></td>
              <td><?php echo $resultList['mailStatus']; ?></td>
              <td><?php echo $resultList['smsStatus']; ?></td>
              <td><div class="gvre"> 
                <?php if($resultList['PdfName'] != '') { ?>
                <a href="<?php echo $serverurlapi; ?>HOOperation/rejectionpdf/<?php echo $resultList['PdfName'] ?>" target='_blank' class="btn btn-default branchbtn" style="width: unset;">PDF</a>
                  <?php } ?>
                </div>
              </td>
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
      </div>
    </div>
  </div>
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
<!--search filter-->
<script>
// function searchingName(){
//     var name = $("#bname").val().toLowerCase();
//     $("#tablesearch tr").filter(function() {
//       $(this).toggle($(this).text().toLowerCase().indexOf(name) > -1)
//     });
// }
</script>
