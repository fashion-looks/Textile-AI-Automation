<?php 
// get url
include "inc.php";
include "logincheck.php";

$searching = '{
    "Type":"'.$_POST['Type'].'",
    "invoiceNo":"'.$_POST['invoiceNo'].'",
    "fromDate":"'.date('Y-m-d',strtotime($_POST['fromDate'])).'",
    "toDate":"'.date('Y-m-d',strtotime($_POST['toDate'])).'"
}';


if($_POST['action']=="searchaction")
{

$url = $serverurlapi."General/gstReportAPI.php";
logger($InfoMessage."search invoice API post list- ".$searching); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS,$searching);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);

//logger("RESPONSE RETURN FROM PAN/TAN Commission list: ". $result);
$resultData = json_decode($result, true);
curl_close($ch);
}

if($_POST['generateaction']=="generateinvoice")
{
    $invoiceJson = '';
	foreach($_POST['invoiceCheckSingle'] as $DocumentNumber){
		$invoiceJson.= '{"DocumentNumber":"'.$DocumentNumber.'"},';
	}
	
	$jsonPost = '{
		"UserId":"'.$_SESSION['UID'].'",
		"ip":"'.$_SERVER["REMOTE_ADDR"].'",
		"listofinvoice":['.rtrim($invoiceJson,',').']
	}';
	
	logger($InfoMessage."generate invoice API post list- ".$jsonPost); 
    $posturl = $serverurlapi."HOOperation/generategstinvoiceapi.php";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $posturl);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);

    logger("RESPONSE RETURN FROM generate invoice api: ". $result);
    $resultgenerate = json_decode($result, true);
    curl_close($ch);
}

if($_POST['generateaction']=="exportinvoice"){
    logger($InfoMessage."*******INSIDE EXPORT INVOICE ACTION START*******"); 
    $invoiceJson = '';
    $foldername = 'Invoice_'.time();

    if(is_dir($foldername)){
        $currentTFolder = $foldername;
    }else{
        $currentTFolder = mkdir($foldername);
        chmod($foldername,0777);
    }
	
    foreach($_POST['invoiceCheckSingle'] as $DocumentNumber){
        $fetchurl = $serverurlapi."HOOperation/invoice/".$DocumentNumber."_INV.pdf";
        $copyPath = $foldername.'/'.$DocumentNumber.'_INV.pdf';
        $pdfContent = file_get_contents($fetchurl);
        file_put_contents ($copyPath, $pdfContent);
        chmod($copyPath,0777);
    }

    $path = 'data/temp/invoices/';

    ///Create zip file
	$zip = new \ZipArchive();
	$filename123 = $path.$foldername.".zip";
    if ($zip->open($filename123, ZipArchive::CREATE)!==TRUE) {
		exit("cannot open <$filename123>\n");
	}

    $dir = ''.$foldername.'/';
	//function to Create zip
	createZip($zip,$dir);
	$zip->close();

    ///Download zip file
	$filenamenew = $foldername.".zip";

    header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.$filenamenew.'"');
	header("Content-Transfer-Encoding: binary");
	//header("Content-Length: ".filesize($filepath));
	ob_end_flush();
	@readfile($filename123);

    //delete directory
    delete_directory($foldername);
    ///Zip file delete
	unlink($filename123);

    logger($InfoMessage."*******EXPORT INVOICE ACTION END*******");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Generate Invoice</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
    <?php include 'links.php'; ?>
    <script>
    $(document).ready(function() {
        $('#datatable').DataTable();
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
            <?php if($resultgenerate['Message']!=''){ ?>
            <div class="bs-example" style="padding-top: 14px;padding-left: 19px;padding-right: 19px;" id="divMsg">
                <!-- Success Alert -->
                <div class="alert alert-dismissible fade show" style="border: solid 2px;border-block-color: green; ">
                    <span id="msg"><?php echo $resultgenerate['Message']; ?></span>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            </div>
            <?php } ?>
            <section>
                <div class="container-fluid">
                    <form action="" method="POST" autocomplete="nope" id="exportfrm" />
                    <div class="row gy-bvc">
                        <div class="col-md-2">
                            <h6 style="font-weight: initial;">Invoice No</h6>
                            <input type="text" name="invoiceNo" class="form-control "
                                value="<?php echo $_POST["invoiceNo"]; ?>">
                        </div>
                        <div class="col-md-2">
                            <h6 style="font-weight: initial;">Selected Date</h6>
                            <select class="form-control" name="Type">
                                <option value="import" <?php if($_POST['Type']=="import"){ echo 'selected'; } ?>>
                                    Imported Date
                                </option>
                                <option value="document" <?php if($_POST['Type']=="document"){ echo 'selected'; } ?>>
                                    Document Date
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <h6 style="font-weight: initial;">From Date</h6>
                            <input type="text" name="fromDate" class="form-control datepicker"
                                value="<?php echo $_POST["fromDate"]; ?>" readonly="readonly">
                        </div>
                        <div class="col-md-2">

                            <h6 style="font-weight: initial;">To Date</h6>
                            <input type="text" name="toDate" class="form-control datepicker"
                                value="<?php echo $_POST["toDate"]; ?>" readonly="readonly">

                        </div>

                        <div class="col-md-3">
                            <div>
                                <h6>&nbsp;</h6>
                                <input type="hidden" id="action" name="action" value="" />
                                <input type="button" name="Search" class="btn btn-success"
                                    onClick="searchFunc('searchaction');" value="Search" />

                                <!--<input type="button" name="Search" class="btn btn-success"
                                    onClick="searchFunc('exportaction');" value="Export Invoice" />
                                ---->
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

                /*function searchFunc(data){
                    $('#action').val(data);
                    var branch = $('#Branch').val();
                    if(branch == ''){
                        $('#Branch').focus().css("border-color","#c00101");
                    }else{
                        $('form#exportfrm').submit();
                    }
                }**/

                $(document).ready(function() {
                    $('select').selectize({
                        sortField: 'text'
                    });
                });
                </script>
                <script>
                $(function() {
                    $(".datepicker").datepicker({
                        dateFormat: 'dd-mm-yy',
                        maxDate: 0
                    });
                });
                </script>
            </section>
            <form method="post" action="" id="invoicefrm">
                <input type="hidden" name="generateaction" id="generateaction" value="">
                <div class="container-fluid" id="tabledata">
                    <div class="generateinvoiceCls" style="display:none; float:left;">
                        <input type="button" name="Search" class="btn btn-success"
                            style="font-size: 13px; font-weight: 700;" onClick="searchFuncInv('generateinvoice');" value="Generate Invoice ">&nbsp;&nbsp;
                        <input type="button" name="Search" class="btn btn-success"
                            style="font-size: 13px; font-weight: 700;" onClick="searchFuncInv('exportinvoice');" value="Export Invoice">
                    </div>

                    <script>
                    function searchFuncInv(data) {
                        $('#generateaction').val(data);
                        $('form#invoicefrm').submit();
                    }
                    </script>
                    <table id="datatable" class="table table-striped table-bordered table-responsive"
                        style="width:100%">
                        <thead>
                            <tr class="vcx-i hgt">
                                <th><input name="invoiceCheckAll" type="checkbox" class="" id="invoiceCheckAll"
                                        style="height: 17px;width: 50px;margin-top: 0;text-align: center;" /></th>
                                <th>BranchCode</th>
                                <th>BranchEmail</th>
                                <th>IRNno</th>
                                <th>QrCode</th>
                                <th>IRNDate</th>
                                <th>DocType</th>
                                <th>DocNum</th>
                                <th>DocDate</th>
                                <th>SupplierGSTIN</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tablesearch">
                            <?php
    if(isset($resultData['GstData'])){                    
    $no=1;
    $crTotal = 0;
    $drTotal = 0;
    foreach($resultData['GstData'] as $rowdata){
        foreach($rowdata['OutputJson'] as $resultList){
            if($resultList['Irn']!=''){
    ?>
                            <tr class="uyt hgte">
                                <td><input type="checkbox"
                                        style=" opacity:1; cursor: pointer; height: 17px; width: 50px; margin-top: 3px;"
                                        value="<?php echo $resultList['DocumentNumber']; ?>" name="invoiceCheckSingle[]"
                                        class="deleteack" /></td>
                                <td><?php echo $rowdata['BranchCode']; ?></td>
                                <td><?php echo $rowdata['BranchEmail']; ?></td>
                                <td><?php echo substr($resultList['Irn'],0,20); ?>...</td>
                                <td><?php echo substr($resultList['SignedQRCode'],0,20); ?>...</td>
                                <td><?php echo $resultList['IrnGenDt']; ?></td>
                                <td><?php echo $resultList['DocumentType']; ?></td>
                                <td><?php echo $resultList['DocumentNumber']; ?></td>
                                <td><?php echo $resultList['DocumentDate']; ?></td>
                                <td><?php echo $resultList['SupplierGSTIN']; ?></td>
                                <td><?php if($rowdata['PdfUrl']!=''){ ?><a href="<?php echo $rowdata['PdfUrl']; ?>"
                                        target="BLANK__">Download
                                        Invoice</a><?php }else{ echo 'Invoice not generated'; } ?></td>
                            </tr>
                            <?php
   } $no++;} } ?>

                            <?php }else{?>
                            <tr class="uyt hgte">
                                <td colspan="11">
                                    <div align="center"><?php echo 'You Can Search...'; ?></div>
                                </td>
                            </tr>
                            <?php }
    ?>
                        </tbody>
                    </table>

                </div>
            </form>
        </div>
    </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        // check uncheck all inclusions
        $("#invoiceCheckAll").click(function() {
            if (this.checked) {
                $('.deleteack').each(function() {
                    this.checked = true;
                })
            } else {
                $('.deleteack').each(function() {
                    this.checked = false;
                })
            }
        });

    });

    window.setInterval(function() {
        checked = $("#tabledata input[type=checkbox]:checked").length;
        if (!checked) {
            $(".generateinvoiceCls").hide();
        } else {

            $(".generateinvoiceCls").show();
        }
    }, 100);
    </script>
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
<!--search filter-->
<script>
function searchingName() {
    var name = $("#bname").val().toLowerCase();
    $("#tablesearch tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(name) > -1)
    });
}
</script>
