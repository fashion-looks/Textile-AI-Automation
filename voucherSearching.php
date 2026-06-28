 <?php 
// get url
 include "inc.php";
 include "logincheck.php";
 $InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;
 logger($InfoMessage." URL for API - ".$url); 
 if(isset($_POST['Search']))
 {
   $searching = '{
    "fromDate": "'.$_POST['fromDate'].'",
    "toDate": "'.$_POST['toDate'].'",
    "voucherNo": "'.$_POST['voucherNo'].'",
    "voucherType": "'.$_POST['voucherType'].'",
    "chequeNo": "'.$_POST['chequeNo'].'",
    "accountName": "'.$_POST['accountName'].'"
  }';
  $url = $serverurlapi."General/voucherSearchingAPI.php";
  $response = postCurlData($url,$searching);
  logger("Response return from Voucher Searching API SEARCH: ". $response); 
  $regionData = json_decode($response);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Voucher Searching</title>
  <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
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
    <!-- <div style="background:transparent;">

    </div> -->
    
    <div class="container-fluid">
      <form action="" method="post" />
      <div class="row gy-bvc" style="">
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Voucher Number</h6>
            <input class="inp-w" type="text" autocomplete="off" name="voucherNo" value="<?php echo $_POST['voucherNo']; ?>" id="voucherNo">
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">From Date</h6>
            <input class="inp-w datepicker" type="text" autocomplete="off" name="fromDate" value="<?php echo $_POST['fromDate']; ?>" id="fromDate">
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">To Date</h6>
            <input class="inp-w datepicker" type="text" autocomplete="off" name="toDate" value="<?php echo $_POST['toDate']; ?>" id="toDate">
          </div>
        </div>

        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Voucher Type</h6>
            <select class="inp-w ui-select" name="voucherType">
              <option value="">All</option>
              <option value="bankReceipt">Bank Receipt</option>
              <option value="bankPayment">Bank Payment</option>
              <option value="journalVoucher">Journal Voucher</option>
              <option value="debitNote">Debit Note</option>
              <option value="creditNote">Credit Note</option>
            </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Account Name</h6>
              <select class="inp-w ui-select select-state" name="accountName">
              <option value=""></option>
              <?php 
              $jsonData = '{
          "AccountName":"",
          "GroupId":"",
          "Status":"1"
        }';
        $newurl = $serverurlapi."General/accountNameAPI.php";
        $resultData = postCurlData($newurl,$jsonData);
        logger('Response return from account Name API: '.$resultData);
        $accountData = json_decode($resultData);
        if(isset($accountData->status)=='true'){
        if(isset($accountData->AccountNameData)){                    
        foreach($accountData->AccountNameData as $resultList){
          ?>
          <option value="<?php echo $resultList->Id; ?>"><?php echo $resultList->AccountName; ?></option>
          <?php } } } ?>
          </select>
          </div>
        </div>
        <div class="col-md-2">
          <div class="search-input-grid">
            <h6 style="font-weight: initial;">Cheque Number</h6>
            <input class="inp-w" type="text" autocomplete="off" name="chequeNo" value="<?php echo $_POST['chequeNo']; ?>" id="chequeNo">
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
  </section>
  <div class="container-fluid">
    <table id="datatable" class="table table-striped table-bordered" style="width:100%">
      <thead>
        <tr class="vcx-i hgt">
          <th>S.No</th>
          <th>From Date</th>
          <th>To Date</th>
          <th>Amount</th>
          <th>Commission Type</th>
          <th>Status</th>
          <th>Created By</th>
          <th>Created Date</th>
          <!-- <th>Action</th> -->
        </tr>
      </thead>
      <tbody id="tablesearch">
        <?php
        if(isset($regionData->DataTable)){ 
          $no=1;
          foreach($regionData->DataTable as $resultList){
            ?>
            <tr class="uyt hgte">
              <td><?php echo $no; ?></td>
              <td><?php echo date('d-m-Y',strtotime($resultList->FromDate)); ?></td>
              <td><?php echo date('d-m-Y',strtotime($resultList->ToDate)); ?></td>
              <td><?php echo $resultList->CommissionRate; ?></td>
              <td><?php if($resultList->CommissionType=='A'){echo 'Acceptance'; }elseif($resultList->CommissionType=='D'){ echo 'Digitization'; }elseif($resultList->CommissionType=='I'){ echo 'Incentive'; } ?></td>
              <td><?php if($resultList->Status==1){echo 'Active';} else{ echo 'Inactive'; } ?></td>
              <td><?php echo $resultList->CreatedBy; ?></td>
              <td><?php echo $resultList->CreatedDate; ?></td>
              <!-- <td><div class="gvre"> <a href="addCommissionValidity.php?editId=<?php // echo encode($resultList->Id); ?>" class="btn btn-default branchbtn" style="width: 100%;">Edit</a></div></td> -->
            </tr>
            <?php
            $no++;
          }
        }
        if($no==1){
         ?>
         <tr class="uyt hgte">
          <td colspan="8"><div align="center"><?php echo 'No Result Found...'; ?></div></td>    
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</div>
</div>
</div>
<script>
  $( function() {
    $( ".datepicker" ).datepicker({ 
      dateFormat: 'dd-mm-yy',
    });
  } );


    $(document).ready(function () {
      $('.select-state').selectize({
          sortField: 'text'
      });
  });
</script>
<?php include 'footer.php'; ?>
</body>
</html>
<style>
  .selectize-input{
    border: 1px solid #767676!important;
    border-radius: 2px!important; 
}
.selectize-control{
  padding: 0!important;
}
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
