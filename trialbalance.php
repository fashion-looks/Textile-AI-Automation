<?php 
// get url
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;

 
if($_POST['action']=="searchaction"){


$fromDate = date('Y-m-d',strtotime($_POST['fromDate']));
$toDate = date('Y-m-d',strtotime($_POST['toDate']));


$searching = '{
    "ledgerType":"'.$_POST['ledgerType'].'",
    "fromDate":"'.$fromDate.'",
    "toDate":"'.$toDate.'"
}';

$url = $serverurlapi."General/trialbalanceAPI.php";
logger($InfoMessage." URL for list API - ".$searching); 
logger($InfoMessage." URL for list API - ".$url); 

$result = postCurlData($url,$searching);
$DataArr = json_decode($result, true);
logger($InfoMessage." RESPONSE RETURN FROM TRIAL BALANCE API : ".$result);
}
 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Trial Balance</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
    <?php include 'links.php'; ?>
    <script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "lengthMenu": [50, 100, 250, 500, 1000],
            "pageLength": 100

        });
    });
    </script>
    <!-- Favicon -->
</head>

<body>
    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav hk-nav-toggle">
        <!-- Top Navbar -->
        <?php include 'backofficeheader.php'; ?>
        <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
        <div class="hk-pg-wrapper">

            <div class="container-fluid">
                <form action="" method="POST" autocomplete="off" id="exportfrm" />
                <div class="row gy-bvc">
                    <div class="col-md-4">
                        <div class="flx">
                            <h6 style="font-weight: initial;">Trial&nbsp;Balance</h6>
                            <select class="inp-w ui-select" name="ledgerType">
                                <option value="">Select</option>
                                <option value="1"
                                    <?php if($_POST['ledgerType']=='1'){ ?>selected="selected"<?php } ?>>Trail Balance (General Ledgre) Normal</option>
                                <option value="2"
                                    <?php if($_POST['ledgerType']=='2'){ ?>selected="selected"<?php } ?>>Trail Balance (General Ledgre) Grouped</option>
                                <option value="3"
                                    <?php if($_POST['ledgerType']=='3'){ ?>selected="selected"<?php } ?>>Trail Balance (Sub-Ledgre) Normal</option>
                                <option value="4"
                                    <?php if($_POST['ledgerType']=='4'){ ?>selected="selected"<?php } ?>>Trail Balance (Sub-Ledgre) Grouped</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="flx">
                            <h6 style="font-weight: initial;">From&nbsp;Date</h6>
                            <input type="text" name="fromDate" class="form-control datepicker"
                                value="<?php echo $_POST['fromDate']; ?>" required autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="flx">
                            <h6 style="font-weight: initial;">To&nbsp;Date</h6>
                            <input type="text" name="toDate" class="form-control datepicker"
                                value="<?php echo $_POST['toDate']; ?>" required autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="flx">
                            <input type="hidden" id="action" name="action" value="" />
                            <input type="button" name="Search" class="btn btn-success"
                                onClick="searchFunc('searchaction');" value="Search" />
                            <!-- <input type="button" name="Search" class="btn btn-success" onClick="searchFunc('exportaction');" value="Export Data" /> -->
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <script>
            function searchFunc(data) {
                $('#action').val(data);
                $('form#exportfrm').submit();
            }
            </script>


            <div class="container-fluid" style="margin-top:20px;overflow: auto;">
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr class="vcx-i hgt">
                            <th>Particulers</th>
                            <th>Credit</th>
                            <th>Debit</th>
                        </tr>
                    </thead>
                    <tbody id="tablesearch">
                        <?php
                        if(isset($DataArr['Status'])=='true'){
                        if(isset($DataArr['TotalData'])){             
                        $no=1;
                        $crTotal=0;
                        $drTotal=0;
                        foreach($DataArr['TotalData'] as $resultList){
                        ?>
                        <tr class="uyt hgte">
                            <td><?php echo $resultList['AccountName']; ?></td>
                            <td><?php echo $resultList['Credit']; $crTotal+=$resultList['Credit'];   ?></td>
                            <td><?php echo $resultList['Debit']; $drTotal+=$resultList['Debit']; ?></td>
                        </tr>
                        <?php
                    $no++;
                    } ?>
                    <tr class="uyt hgte" style="background:lightgrey;">
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo $crTotal; ?></strong></td>
                            <td><strong><?php echo $drTotal; ?></strong></td>
                    </tr>
                    <?php
                    }
                    }else{ ?>
                        <tr class="uyt hgte">
                            <td colspan="15">
                                <div align="center"><?php echo 'You Can Search...'; ?></div>
                            </td>
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
<style>
.ui-select {
    padding: 2%;
}

.hgt th {
    text-align: center;
    font-weight: bold;
}

.hgte td {
    text-align: center;
}

.gvre {
    display: flex;
    column-gap: 10px;
}

.lk-kl {
    width: fit-content;
    margin-left: auto;
    column-gap: 50px;
}

.pd-btn {
    padding: 3px 40px;
}

.pd-btn2 {
    padding: 3px 80px;
}

table.dataTable>thead>tr>th:not(.sorting_disabled),
table.dataTable>thead>tr>td:not(.sorting_disabled),
table.table-bordered.dataTable tbody th,
table.table-bordered.dataTable tbody td {
    font-size: 12px !important;
}

.flx {
    display: flex;
    column-gap: 12px;
}

.search-input-grid {
    display: grid;
    grid-gap: 5px;
}

.search-button {
    display: grid;
    grid-template-columns: auto auto;
    grid-gap: 20px;
}

.vcx-i {
    border-top: 2px solid;
    border-bottom: 2px solid;
}

.ht-jy {

    margin-top: 7%;
}

.inp-wuui {
    margin: 3px;
}

.gy-bvc {
    margin: 1%;
}

.nn-mb {
    margin-top: 3%;
}

.inp-w {
    width: 90%;
}

.uyt td {
    border: none;
}
</style>
<script>
$(function() {
    $(".datepicker").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: 0
    });
});
</script>
<!--search filter-->
<script>
// function searchingName(){
//     var name = $("#bname").val().toLowerCase();
//     $("#tablesearch tr").filter(function() {
//       $(this).toggle($(this).text().toLowerCase().indexOf(name) > -1)
//     });
// }
</script>
