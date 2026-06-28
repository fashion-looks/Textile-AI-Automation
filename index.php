<?php
include "inc.php";
include "logincheck.php";
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Dashboard | <?php echo $systemName; ?></title>
    <meta name="description" content="PAN OFFICE" />
    <?php include 'links.php'; ?>
</head>

<body id="autorefresh">
    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav hk-nav-toggle">
        <!-- Top Navbar -->
        <?php include 'backofficeheader.php'; ?>
        <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
        <div class="hk-pg-wrapper">
            <div
                style="background-color: #1e733f;background-image:linear-gradient(to right,#1e733f,#79c117);padding:3px;">
                <marquee>
                    <div class="Container-fluid">
                        <div class="row strip">
                            <div class="col-sm-5">
                                <p class="ticker">The best accountants don't just interpret numbers; they decipher the language of financial success</p>
                            </div>
                        </div>
                    </div>
                </marquee>
            </div>
            <link rel="stylesheet" href="css/font-awesome.min.css">
            <!-- Row -->
            <div class="row">
                <div class="col-xl-12">
                    <div id="loadtable" style="padding:30px;">
                        <div style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: #000000c7; z-index: 9999; display:none;"
                            id="blkbox">

                            <div
                                style="padding:20px; background-color:#FFFFFF; margin:auto; width:300px; margin-top:10%; text-align:center; border-radius: 10px;color: green;">
                                <img src="img/Spin2.gif" width="100px;"><br>
                                Loading... Please wait</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
    $(document).ready(function() {
        //setInterval(function(){
        //   $("#loadtable").load("loadindexdata.php");
        //}, 95000);
    });

    function funcLoadTable() {
        $('#blkbox').show();
        $("#loadtable").load(
            "loadindexdata.php?aid=<?php echo $_GET['aid']; ?>&uploadType=<?php echo $_GET['uploadType']; ?>&productType=<?php echo $_GET['productType']; ?>&formType=<?php echo $_GET['formType']; ?>&subStage=<?php echo $_GET['subStage']; ?>&action=<?php echo $_GET['action']; ?>"
            );
    }
    funcLoadTable();
    </script>
</body>

</html>