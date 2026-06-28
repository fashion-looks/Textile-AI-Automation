<?php
// get url
include "inc.php";
include "logincheck.php";
$InfoMessage = "[Info] - File location ".$_SERVER['PHP_SELF']." Message:- " ;


if($_POST['action']=="searchaction"){

if($_POST['fromDate']!='' || $_POST['toDate']!=''){
    $fromDate = date('Y-m-d',strtotime($_POST['fromDate']));
    $toDate = date('Y-m-d',strtotime($_POST['toDate']));
}

$searching = '{
    "fromDate":"'.$fromDate.'",
    "toDate":"'.$toDate.'"
}';

$url = $serverurlapi."General/collectionReportAPI.php";
logger($InfoMessage." URL for list API - ".$searching);
logger($InfoMessage." URL for list API - ".$url);

$result = postCurlData($url,$searching);
$DataArr = json_decode($result, true);
//logger($InfoMessage." RESPONSE RETURN FROM COLLECTION REPORT LIST API : ".$result);
}

if($_POST['action']=="exportaction"){

    if($_POST['fromDate']!='' || $_POST['toDate']!=''){
        $fromDate = date('Y-m-d',strtotime($_POST['fromDate']));
        $toDate = date('Y-m-d',strtotime($_POST['toDate']));
    }

    $searching = '{
        "fromDate":"'.$fromDate.'",
        "toDate":"'.$toDate.'"
    }';

    $url = $serverurlapi."General/collectionReportAPI.php";
    logger($InfoMessage." URL for export API - ".$searching);
    logger($InfoMessage." URL for export API - ".$url);

    $result = postCurlData($url,$searching);
    $regionData = json_decode($result, true);
    //logger($InfoMessage."RESPONSE RETURN FROM COLLECTION REPORT API : ".$result);

//Filter the excel data
function filterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// Excel file name for download
$fileName = "Collection_Report.xls";

// Column names
$fields = array('Branch Code', 'Exist Code', 'Branch Name', 'Branch Type', 'No of PAN Resident ', 'No of PAN NRI', 'PAN Amount', 'No of App TAN', 'TAN Amount', '< 100 eTDS', '100 To 1000 eTDS', '> 1000 eTDS', 'Return Amount', '< 100 24G', '100 To 1000 24G', '> 1000 24G', '24G Amount', 'No of Mobilr PAN Resident ', 'No of Mobile PAN NRI', 'Mobile PAN Amount','Total Amount');

// Display column names as first row
$excelData = implode("\t", array_values($fields)) . "\n";

	if(isset($regionData['Status'])=='true'){
		if(isset($regionData['TotalData'])){
			$no=1;

			foreach($regionData['TotalData'] as $resultList){
			 $lineData = array($resultList['BranchCode'],$resultList['ExistCode'], $resultList['BranchName'], $resultList['BranchType'], $resultList['NoPanResident'], $resultList['NoPanNRI'], $resultList['PanAmount'], $resultList['NoAppTan'],$resultList['TanAmount'],$resultList['Less100eTds'],$resultList['eTds100_1000'],$resultList['More1000eTds'],$resultList['ReturnAmount'],$resultList['Less100g24'],$resultList['g24100_1000'],$resultList['More1000g24'],$resultList['G24Amount'],$resultList['NomPanResident'], $resultList['NomPanNRI'], $resultList['MPanAmount'],$resultList['TotalAmount']);

             array_walk($lineData, 'filterData');
            $excelData .= implode("\t", array_values($lineData)) . "\n";

            $no++;
			}

		}
	}

    // Headers for download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    // Render excel data
    echo $excelData;

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Collection Report</title>
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
        <div class="hk-pg-wrapper" >

            <div class="container-fluid">
                <form action="" method="POST" autocomplete="off" id="exportfrm" />
                <div class="row gy-bvc">
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
            function searchFunc(data) {
                $('#action').val(data);
                $('form#exportfrm').submit();
            }
            </script>


            <div class="container-fluid" style="margin-top:20px;overflow: auto;">
                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr class="vcx-i hgt">
                            <th>Branch Code</th>
                            <th>Exist Code</th>
                            <th>Branch Name</th>
                            <th>Branch Type</th>
                            <th>No of PAN Resident</th>
                            <th>No of PAN NRI</th>
                            <th>PAN Amount</th>
                            <th>No of App TAN</th>
                            <th>TAN Amount</th>
                            <th>< 100 eTDS</th>
                            <th>100 To 1000 eTDS</th>
                            <th>> 1000 eTDS</th>
                            <th>Return Amount</th>
                            <th>< 100 24G</th>
                            <th>100 To 1000 24G</th>
                            <th>> 1000 24G</th>
                            <th>24G Amount</th>
                            <th>No of Mobile PAN Resident</th>
                            <th>No of Mobile PAN NRI</th>
                            <th>Mobile PAN Amount</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody id="tablesearch">
                        <?php
                        if(isset($DataArr['Status'])=='true'){
                        if(isset($DataArr['TotalData'])){
                        $no=1;
                        foreach($DataArr['TotalData'] as $resultList){
                        ?>
                        <tr class="uyt hgte">
                            <td><?php echo $resultList['BranchCode']; ?></td>
                            <td><?php echo $resultList['ExistCode'] ?></td>
                            <td><?php echo $resultList['BranchName']; ?></td>
                            <td><?php echo $resultList['BranchType']; ?></td>
                            <td><?php echo $resultList['NoPanResident']; ?></td>
                            <td><?php echo $resultList['NoPanNRI']; ?></td>
                            <td><?php echo $resultList['PanAmount']; ?></td>
                            <td><?php echo $resultList['NoAppTan']; ?></td>
                            <td><?php echo $resultList['TanAmount']; ?></td>
                            <td><?php echo $resultList['Less100eTds']; ?></td>
                            <td><?php echo $resultList['eTds100_1000']; ?></td>
                            <td><?php echo $resultList['More1000eTds']; ?></td>
                            <td><?php echo $resultList['ReturnAmount']; ?></td>
                            <td><?php echo $resultList['Less100g24']; ?></td>
                            <td><?php echo $resultList['g24100_1000']; ?></td>
                            <td><?php echo $resultList['More1000g24']; ?></td>
                            <td><?php echo $resultList['G24Amount']; ?></td>
                            <td><?php echo $resultList['NomPanResident']; ?></td>
                            <td><?php echo $resultList['NomPanNRI']; ?></td>
                            <td><?php echo $resultList['MPanAmount']; ?></td>
                            <td><?php echo $resultList['TotalAmount']; ?></td>
                        </tr>
                        <?php
                    $no++;
                    }
                    }
                    }else{ ?>
                        <tr class="uyt hgte">
                            <td colspan="21">
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