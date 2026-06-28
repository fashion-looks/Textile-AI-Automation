<?php 
// get url
include 'inc.php';
include "logincheck.php";

//view user list

$url = $serverurlapi."General/settingsAPI.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
logger("Response from setting api: ".$result);
$userData = json_decode($result, true);
curl_close($ch)

?>
<?php 
//insert setting information
if(isset($_POST['adduser']) && $_POST['action']!=''){
$postData = '{
  "Id":"'.trim($_POST['editId']).'",
  "Name":"'.trim($_POST['Name']).'",
  "Data_Entry_Flag":"'.$_POST['Data_Entry_Flag'].'"
}';
//use curl method
$ch = curl_init();
$url = $serverurlapi."General/updateSettingAPI.php";
logger('settingdata post: :'.$postData);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:multipart/form-data;'));
$resultData = curl_exec($ch);
logger('RESPONCE FROM SETTING PAGE :'.$resultData);
curl_close($ch);
$resultDataArr = json_decode($resultData, true);

$_SESSION['error']=$resultDataArr['Message'];

//header('Location: settings.php');

}
//die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Setting</title>
<meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
<!-- Favicon -->
<?php include 'links.php'; ?>
</head>
<body>
<!-- HK Wrapper -->
<div class="hk-wrapper hk-vertical-nav">
  <!-- Top Navbar -->
  <?php include 'header.php'; ?>
  <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
  <div class="hk-pg-wrapper" >
  <?php if(isset($_SESSION['error'])!=''){ ?>
		  <div class="bs-example" style="padding-top: 14px;padding-left: 19px;padding-right: 19px;"> 
			<!-- Success Alert -->
			<div class="alert alert-dismissible fade show" style="border: solid 2px;border-block-color: green;">
				 <?php echo $_SESSION['error'];unset($_SESSION['error']); ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
		    </div>
		  </div>
  <?php } ?>
   
    <div class="container" style="margin-bottom: 37px; margin-top: 32px;padding-left: 20px; padding-right: 20px;">
      <form action="settings.php" method="post">
      <div class="form-group row">
        <label for="UserId" class="col-sm-2 col-form-label">Name: </label>
        <div class="col-sm-4">
          <input type="text" class="form-control" name="Name" id="name" value="<?php echo $userData['Name']; ?>" readonly>
          <p id="useridcheck"></p>
        </div>
        <label for="staticEmail" class="col-sm-2 col-form-label">Data Entry 2 Flag: </label>
        <div class="col-sm-4">
          <select class="inp-w ui-select wd-tr valid" name="Data_Entry_Flag">
            <option value="0" <?php if($userData['Data_Entry_Flag']=='0'){ echo 'selected';  } ?>>No</option>
            <option value="1" <?php if($userData['Data_Entry_Flag']=='1'){ echo 'selected';  } ?>>Yes</option>
          </select>
          <p id="useremailcheck"></p>
        </div>
      </div>
      
      <div class="row">
      <div class="col">
          <div class="">
            
          </div>
        </div>
        <div class="col">
          <div class="">
           
          </div>
        </div>
        <div class="col">
          <div class="">
            
          </div>
        </div>
        <div class="col">
          <div class="">
            <input type="hidden" name="action" value="settingaction">
           
            <input type="hidden" name="editId" value="<?php echo $userData['Id']; ?>">
            <input type="submit" name="adduser" class="browsebutton" id="usersubmit" value="Save">
          </div>
        </div>
       
        
        
      </div>
      </form>

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
