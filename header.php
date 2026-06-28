<nav class="navbar navbar-expand-xl navbar-dark fixed-top hk-navbar" style="background:white;">
  <!-- <a tabindex="-1" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" href="javascript:void(0);"><span class="feather-icon"><i data-feather="more-vertical"></i></span></a> -->
  <a tabindex="-1" id="navbar_toggle_btn" class="navbar-toggle-btn nav-link-hover" href="javascript:void(0);"><span class="feather-icon"><i data-feather="menu"></i></span></a> <a tabindex="-1" class="navbar-brand" href="home">
    <img class="brand-img d-inline-block" style="width: 185px;" src="img/Religare-Logo.png" alt="brand" /></a>
  <style>
    .feather-icon svg {
      color: #757575;
    }
  </style>
  <span style="color: #757575; font-size: 15px; font-weight: 600;"> >>
    <?php
    if (strpos($_SERVER['REQUEST_URI'], "filesubmit") !== false) {
      echo "File Submit PAN";
    } elseif (strpos($_SERVER['REQUEST_URI'], "filesubmittan") !== false) {
      echo 'File Submit TAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "bulkuploaddata") !== false) {
      echo 'Bulk Upload PAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "bulkuploaddatatan") !== false) {
      echo 'Bulk Upload TAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "index") !== false) {
      echo 'Dashboard';
    } elseif (strpos($_SERVER['REQUEST_URI'], "missubmit.php?t=1") !== false) {
      echo 'MIS Upload PAN/TAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "missubmit.php?t=2") !== false) {
      echo 'MIS Upload eTDS';
    } elseif (strpos($_SERVER['REQUEST_URI'], "missubmit.php?t=3") !== false) {
      echo 'MIS Upload 24G';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listbranch") !== false) {
      echo 'Branch List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "branch1") !== false) {
      echo 'Add/Edit Branch';
    } elseif (strpos($_SERVER['REQUEST_URI'], "dataentrytan") !== false) {
      echo 'Data Entry TAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "dataentry") !== false) {
      echo 'Data Entry PAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addAO") !== false) {
      echo 'Add AO';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addFieldList") !== false) {
      echo 'Add Field List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addho") !== false) {
      echo 'Add HO';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addisdecode") !== false) {
      echo 'Add Isde Code';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addItemList") !== false) {
      echo 'Add Item List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addregion") !== false) {
      echo 'Add/Edit Region';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addrejection") !== false) {
      echo 'Add Rejection';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addrloffice") !== false) {
      echo 'Add Office';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addstate") !== false) {
      echo 'Add State';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addvendor") !== false) {
      echo 'Add Vendors';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addVendorMapping") !== false) {
      echo 'Vendor Mapping';
    } elseif (strpos($_SERVER['REQUEST_URI'], "auditlogpan") !== false) {
      echo 'Audit Log PAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "auditlogtan") !== false) {
      echo 'Audit Log TAN';
    } elseif (strpos($_SERVER['REQUEST_URI'], "batchsuccessuploadfile") !== false) {
      echo 'Batch Success Upload file';
    } elseif (strpos($_SERVER['REQUEST_URI'], "batchuploadfile") !== false) {
      echo 'Batch Upload file';
    } elseif (strpos($_SERVER['REQUEST_URI'], "batchuploadfileparse") !== false) {
      echo 'Batch Upload file Parse';
    } elseif (strpos($_SERVER['REQUEST_URI'], "batchuploadsuccessfileparse") !== false) {
      echo 'Batch Upload Success file Parse';
    } elseif (strpos($_SERVER['REQUEST_URI'], "boxmanagement") !== false) {
      echo 'Box Management';
    } elseif (strpos($_SERVER['REQUEST_URI'], "branchbalance") !== false) {
      echo 'Branch Balance';
    } elseif (strpos($_SERVER['REQUEST_URI'], "bulksubmit") !== false) {
      echo 'Bulk Submit';
    } elseif (strpos($_SERVER['REQUEST_URI'], "callAcknowData") !== false) {
      echo 'Call Acknowledgement Data';
    } elseif (strpos($_SERVER['REQUEST_URI'], "checkacklist") !== false) {
      echo 'Check Acknowledgement List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "courierreceiving") !== false) {
      echo 'Courier Receiving';
    } elseif (strpos($_SERVER['REQUEST_URI'], "cropingstage") !== false) {
      echo 'Croping Stage';
    } elseif (strpos($_SERVER['REQUEST_URI'], "documentsform") !== false) {
      echo 'Documents Form';
    } elseif (strpos($_SERVER['REQUEST_URI'], "documentsform1cr") !== false) {
      echo 'Documents Form CR';
    } elseif (strpos($_SERVER['REQUEST_URI'], "documentsform2") !== false) {
      echo 'Documents Form 2';
    } elseif (strpos($_SERVER['REQUEST_URI'], "downloadpdf") !== false) {
      echo 'Documents PDF';
    } elseif (strpos($_SERVER['REQUEST_URI'], "entry2") !== false) {
      echo 'Entry 2';
    } elseif (strpos($_SERVER['REQUEST_URI'], "exportrebatching") !== false) {
      echo 'Export Rebatching';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listAO") !== false) {
      echo 'List AO';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listbranch") !== false) {
      echo 'List Branch';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listbranch_1") !== false) {
      echo 'List Branch 1';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listho") !== false) {
      echo 'List HO';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listisdecode") !== false) {
      echo 'List Is Decode';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listItem") !== false) {
      echo 'List Item';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listregion") !== false) {
      echo 'List Region';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listrejectionreason") !== false) {
      echo 'List Rejection Region';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listrloffice") !== false) {
      echo 'List Religare Office';
    } elseif (strpos($_SERVER['REQUEST_URI'], "liststate") !== false) {
      echo 'List State';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listvendors") !== false) {
      echo 'List Vendors';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadacknumber") !== false) {
      echo 'Load Acknowledgement Number';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadacknumberlist") !== false) {
      echo 'Load Acknowledgement Number List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadbunchnumber") !== false) {
      echo 'Load Bunch Number';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadcrsubcat") !== false) {
      echo 'Load CR Sub Category';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadcrtitle") !== false) {
      echo 'Load CR Title';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadindexdata") !== false) {
      echo 'Load Index Data';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadindexdata2") !== false) {
      echo 'Load Index Data 2';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadreason") !== false) {
      echo 'Load Reason';
    } elseif (strpos($_SERVER['REQUEST_URI'], "loadsubcat") !== false) {
      echo 'Load Sub Category';
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlaccecptedsubmit") !== false) {
      echo 'NSDL Accecpted Submit';
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlaccecptedsubmittan") !== false) {
      echo 'NSDL Accecpted TAN Submit';
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlpanallotsubmit") !== false) {
      echo 'NSDL PAN Allot Submit';
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlpanallotsubmittan") !== false) {
      echo 'NSDL PAN Allot TAN Submit';
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlstagesubmit") !== false) {
      if ($_GET['type'] == 'batchfile') {
        echo 'NSDL Single Batch Upload PAN';
      }
      if ($_GET['type'] == 'accecptedfile') {
        echo 'NSDL Upload Accecpted PAN';
      }
      if ($_GET['type'] == 'allotmentfile') {
        echo 'NSDL Upload Allotment PAN';
      }
      if ($_GET['type'] == 'batchstatusfile') {
        echo 'NSDL Batch Status';
      }
    } elseif (strpos($_SERVER['REQUEST_URI'], "nsdlstagesubmittan") !== false) {
      if ($_GET['type'] == 'batchfile') {
        echo 'NSDL Single Batch Upload TAN';
      }
      if ($_GET['type'] == 'accecptedfile') {
        echo 'NSDL Upload Accecpted TAN';
      }
      if ($_GET['type'] == 'allotmentfile') {
        echo 'NSDL Upload Allotment TAN';
      }
    } elseif (strpos($_SERVER['REQUEST_URI'], "product") !== false) {
      echo 'Product';
    } elseif (strpos($_SERVER['REQUEST_URI'], "qc_pdf") !== false) {
      echo 'Review';
    } elseif (strpos($_SERVER['REQUEST_URI'], "qc_pdftan") !== false) {
      echo 'Review';
    } elseif (strpos($_SERVER['REQUEST_URI'], "rebatchingpage") !== false) {
      echo 'Re Batching Page ';
    } elseif (strpos($_SERVER['REQUEST_URI'], "rejectionreasonpost") !== false) {
      echo 'Rejection Reason Post';
    } elseif (strpos($_SERVER['REQUEST_URI'], "selecttoexport") !== false) {
      echo 'Export Batch File';
    } elseif (strpos($_SERVER['REQUEST_URI'], "tan_crform") !== false) {
      echo 'TAN CR Form';
    } elseif (strpos($_SERVER['REQUEST_URI'], "tan_newform") !== false) {
      echo 'TAN New Form';
    } elseif (strpos($_SERVER['REQUEST_URI'], "tanform") !== false) {
      echo 'TAN Form';
    } elseif (strpos($_SERVER['REQUEST_URI'], "usercreation") !== false) {
      echo 'User Creation';
    } elseif (strpos($_SERVER['REQUEST_URI'], "usercreation1.php") !== false) {
      echo 'User Creation 1';
    } elseif (strpos($_SERVER['REQUEST_URI'], "vendorboxallot.php") !== false) {
      echo 'Vendor Box Allot';
    } elseif (strpos($_SERVER['REQUEST_URI'], "vendorbranch.php") !== false) {
      echo 'Vendor Branch';
    } elseif (strpos($_SERVER['REQUEST_URI'], "walletbalance.php") !== false) {
      echo 'Wallet Balance';
    } elseif (strpos($_SERVER['REQUEST_URI'], "workingdashboard.php") !== false) {
      echo 'Working Dashboard';
    } elseif (strpos($_SERVER['REQUEST_URI'], "backofficedashboard") !== false) {
      echo 'Back Office Dashboard';
    } elseif (strpos($_SERVER['REQUEST_URI'], "auditTrail.php") !== false) {
      echo 'Audit Trail';
    } elseif (strpos($_SERVER['REQUEST_URI'], "exportnsdlrejection.php") !== false) {
      echo 'Export NSDL Rejection';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listMail.php") !== false) {
      echo 'Rejection Intimation Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "batchstatusreport.php") !== false) {
      echo 'Batch Status Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "downloadpdf1a.php") !== false) {
      echo 'Export PDF/A-1a File';
    } elseif (strpos($_SERVER['REQUEST_URI'], "userGroup.php") !== false) {
      echo 'User Group';
    } elseif (strpos($_SERVER['REQUEST_URI'], "pagePermissionList.php") !== false) {
      echo 'Add Permission';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addBankVoucherEntry.php") !== false) {
      echo 'Add Bank Voucher Entry';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listBankVoucherEntry.php") !== false) {
      echo 'List Bank Voucher Entry';
    } elseif (strpos($_SERVER['REQUEST_URI'], "product.php") !== false) {
      echo 'Product Mapping';
    } elseif (strpos($_SERVER['REQUEST_URI'], "productMaster.php") !== false) {
      echo 'Product Master';
    } elseif (strpos($_SERVER['REQUEST_URI'], "addAccountGroup.php") !== false) {
      echo 'List Account Group';
    } elseif (strpos($_SERVER['REQUEST_URI'], "listAccountGroup.php") !== false) {
      echo 'Add Account Group';
    } elseif (strpos($_SERVER['REQUEST_URI'], "schememaster.php") !== false) {
      echo 'Scheme Master';
    } elseif (strpos($_SERVER['REQUEST_URI'], "docmanagement.php") !== false) {
      echo 'Document Management';
    } elseif (strpos($_SERVER['REQUEST_URI'], "courierlist.php") !== false) {
      echo 'Courier List';
    } elseif (strpos($_SERVER['REQUEST_URI'], "courierdetail.php") !== false) {
      echo 'Add Courier Detail';
    } elseif (strpos($_SERVER['REQUEST_URI'], "settings.php") !== false) {
      echo 'Settings';
    } elseif (strpos($_SERVER['REQUEST_URI'], "ackwisestatusreport.php") !== false) {
      echo 'Ack Wise Status Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "queryscreenreport.php") !== false) {
      echo 'Query Screen Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "userwisereportqc.php") !== false) {
      echo 'User Wise QC Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "vendorreconreport.php") !== false) {
      echo 'Vendor Reconcilation Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "statusreport.php") !== false) {
      echo 'Status Report';
    } elseif (strpos($_SERVER['REQUEST_URI'], "pdf1aconversion_report.php") !== false) {
      echo 'PDF/A-1a Reports';
    } elseif (strpos($_SERVER['REQUEST_URI'], "pdf1adownloadfisher.php") !== false) {
      echo 'Download PDF/A-1a ';
    } elseif (strpos($_SERVER['REQUEST_URI'], "rejectionreport.php") !== false) {
      echo 'Daily PAN/TAN Discrepancy Report';
    }
    ?>


  </span>
  <ul class="navbar-nav hk-navbar-content order-xl-2">
    <?php if (strtoupper($_SESSION['Type']) == "BRANCH") { ?>
      <li class="nav-item dropdown dropdown-authentication">
        <!---- before id goes to documentmanagemnt.php page------->
        <!-- <a href="backofficeindex.php"  class="btn btn-warning" style="background-color: #757575;border-color: #757575;font-size: 13px;" tabindex="-1">Switch To Back Office</a> -->
      </li>
    <?php } ?>
    <li class="nav-item dropdown dropdown-authentication"> <a class="nav-link dropdown-toggle no-caret" tabindex="-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="media">
          <div class="media-img-wrap">
            <div class="avatar"> <img tabindex="-1" src="img/user1.png" alt="user" class="avatar-img rounded-circle"> </div>
            <span class="badge badge-success badge-indicator"></span>
          </div>
          <div class="media-body"> <span style="color:green;"><?php

                                                              if ($_SESSION["Type"] == "BRANCH" || $_SESSION["Type"] == "VENDOR") {
                                                                echo $_SESSION["UserName"] . '[' . getUserType($_SESSION["Type"]) . ' - ' . $_SESSION["BID"] . ']';
                                                              } else {
                                                                echo $_SESSION["UserName"] . '[' . getUserType($_SESSION["Type"]) . ']';
                                                              }

                                                              ?><i class="zmdi zmdi-chevron-down"></i></span> </div>
        </div>
      </a>
      <div tabindex="-1" class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
        <!-- <a tabindex="-1" class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-account"></i><span>Profile</span></a> -->
        <a tabindex="-1" class="dropdown-item" href="#"><i class="dropdown-icon zmdi zmdi-card"></i><span>My balance</span></a>
<?php 
//print_r($_SESSION);

if ($_SESSION['jwtauthToken']): ?>
    <a tabindex="-1" class="dropdown-item" href="sso_switch.php">
        <i class="dropdown-icon zmdi zmdi-card"></i>
        <span>Switch to Tin-FC</span>
    </a>
<?php else: ?>
    <a  tabindex="-1" class="dropdown-item" data-toggle="tooltip" data-placement="top" title="SSO failed: No token found">
   <span style="color: #ffcc00;">&#128683; Switch To Tinfc</span>
</a>   
<?php endif; ?>      

 <?php if ($_SESSION['Type'] == "SUPER") { ?>
          <a tabindex="-1" class="dropdown-item" href="settings.php"><i class="dropdown-icon zmdi zmdi-settings"></i><span>Settings</span></a>
          <a tabindex="-1" class="dropdown-item" href="userGroup.php"><i class="dropdown-icon zmdi zmdi-accounts"></i><span>User Group</span></a>
        <?php } ?>
        <a tabindex="-1" class="dropdown-item" href="logout.php"><i class="dropdown-icon zmdi zmdi-power"></i><span>Log out</span></a>
      </div>
    </li>
  </ul>

</nav>
<!-- Vertical Nav -->
<nav class="hk-nav hk-nav-light" style="background-color: #71b91b;background-image: linear-gradient(#71b91b,#3e8f30"> <a href="javascript:void(0);" id="hk_nav_close" class="hk-nav-close"><span class="feather-icon"><i data-feather="x"></i></span></a>
  <div class="nicescroll-bar">
    <div class="navbar-nav-wrap" style="padding-top: 1.75rem;">
      <ul class="navbar-nav flex-column leftbar">
        <?php
        $headjsonPost = '{
			"userType" : "' . strtoupper($_SESSION['Type']) . '"
		}';
        $headresult = postCurlData($serverurlapi . "General/userHeaderAPI.php", $headjsonPost);
        //logger('RESPONSE RETURN FROM USER PAGE API: '.$headresult);
        $headdata = json_decode($headresult);
        $headDataTable = $headdata->DataTable;
        foreach ($headDataTable as $headmenulist) {
        ?>
          <li class="nav-item"> <a tabindex="-1" class="nav-link" href="<?php echo $headmenulist->PageUrl; ?>"><span class="nav-link-text seq"><?php echo $headmenulist->PageName; ?></span> </a> </li>
        <?php } ?>

        <?php if(strtoupper($_SESSION['Type']) == 'BRANCH'){ ?>
          <li class="nav-item"> <a class="nav-link" href="listBankVoucherEntry.php"><span class="nav-link-text seq">List Bank Voucher</span> </a> </li>
        <?php } ?>

        <li class="nav-item"> <a tabindex="-1" class="nav-link" href="https://www.deboxglobal.com/contact.html"><span class="nav-link-text seq">Helpdesk</span> </a> </li>
      </ul>
    </div>

  </div>
</nav>