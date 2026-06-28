<?php
//error_reporting('E_ALL');
ini_set('display_errors', 1);
include "inc.php";


if(isset($_POST['forgotPassword'])){
    $forgotuserid = trim($_POST['forgotuserid']);
    //logger('Forgot user id: '.$forgotuserid);
    $resetlink = $serverurl.'resetpassword.php?uid='.encode($forgotuserid);
    $jsonpost = '{
        "URL" : "'.$resetlink.'",
        "UserId" : "'.$forgotuserid.'"
    }';
    $url2 = $serverurlapi."HOOperation/forgotpasswordapi.php";
    $result2 = postCurlData($url2,$jsonpost);
  	logger ("RESPONSE FROM FORGOT PAGE: ".$result2);
	$userforgot = json_decode($result2,true);

}


if(isset($_REQUEST['userLogin']))
{

$userId = encode(trim($_POST['userId']));
$password = encode(trim($_POST['password']));

    logger('ui '.$userId.' and pass '.$password);

	$data = array(
		 'userId' => decode($userId),
		 'password' => decode($password)
	);

    logger ("Response from url data jaypal ".json_encode($data));
    $url = $serverurlapi."master/userlogin.php";

	//$result = postCurlData($url,$data);
  	logger ("Response from url ".$url ." is: ".$result);
	//$userlogin = json_decode($result);

    ////////// SSO started ////////////
    //if ($userlogin->Status == 'Success') {
    $aa = "Success";
    if ($aa == 'Success') {
        logger("Json parting value". $userlogin->BranchId);

        $_SESSION["UserName"] = "ShivDVN Accounts";
        //$_SESSION["UID"] = $userlogin->Id;
        $_SESSION["UID"] = 1;
        $_SESSION["userId"] =  decode($userId);
        $_SESSION['sessionid'] = session_id();

        echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
        exit();
    }



}

function decodeaes($encoded) {
$encoded = base64_decode($encoded);
$decoded = "";
for( $i = 0; $i < strlen($encoded); $i++ ) {
$b = ord($encoded[$i]);
$a = $b ^ 10;  $decoded .= chr($a);
}
return base64_decode(base64_decode($decoded));
}
?>
<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo ($_GET['forgot']=='yes') ? 'Forgot Password':'Login'; ?></title>

    <!-- Tell the browser to be responsive to screen width -->

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

   <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

  <![endif]-->



    <!-- Google Font -->

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
    .login-box,
    .register-box {
        width: 380px;
        margin: 5%;
        float: right;
    }

    .login-box-body,
    .register-box-body {
        border-radius: 15px;
        padding: 30px;
        background: #ffffffc4;
        background: #ffffffb8;
        box-shadow: 1px 1px 10px 2px #d8d8d8;
        height: 550px;

    }

    .login-box-msg,
    .register-box-msg {
        color: #6b6a6afa;
        text-align: center;
        margin: 52px;
        font-size: 16px;
        padding: 0px;
        font-family: sans-serif;
        font-weight: 600;
    }

    .sdf23423432 {
        margin-bottom: 0px;
        text-align: center;
    }

    .sdf23423432 img {
        width: 50px;
    }

    .btn-primary {
        background-color: #006b2d;
        border-color: #006b2d;
        padding: 3px;
        border-radius: 5px !Important;
    }

    .center_distance_logo_master {
        width: 50%;
        float: left;
    }

    .sdfsdfsd23423 {
        margin-left: 20%;
    }

    .sdfsdfsd23423 img {
        width: 250px;
    }

    .sdfsd2343254sd {
        margin-top: 15%;
        margin-left: 20%;
    }

    .sdfsd2343254sd img {
        width: 450px;
    }

    .userclass {
        border: 3px solid #8e8e8e;
        border-radius: 50%;
        padding: 14px 22px;
        color: #0f2151;
    }

    .lockclass {
        position: relative;
        float: right;
        border: 2px solid #8e8e8e;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 19px;
        background: white;
        color: black;
        margin-top: -27px;
    }

    .input-icons i {
        position: absolute;
    }

    .input-icons {
        width: 100%;
        margin-bottom: 10px;
    }

    .icon {
        padding: 10px;
        min-width: 40px;
        padding-left: 19px;
        color: #6b6a6afa;

    }

    .input-field {
        width: 100%;
        padding: 14px;
        text-align: center;
    }

    .bb {
        border-radius: 5px;
        border: 1px solid #b7b4b4fa;
    }

    ::placeholder {
        color: #b7b4b4fa;
        font-size: 17px;
        font-weight: 600;

    }
    </style>
	<script>
    window.onload = function() {
        // Select all input elements on the page
        var inputFields = document.querySelectorAll('input');
        // Loop through each input field
        inputFields.forEach(function(input) {
            // Set autocomplete attribute to 'off'
            input.setAttribute('autocomplete', 'off');
        });
    };
</script>

</head>

<body class="hold-transition login-page">

    <div
        style="width: 100%;height:100vh; background-image:url(img/Religare-Login-Page-v2-BG.jpg); background-size:cover;background-repeat:no-repeat;background-position: center;">
        <div class="center_distance_logo_master">
          <!--  <div class="sdfsdfsd23423" style="display:none;"><img src="img/34850983589345.png"></div> -->

        </div>
        <div class="login-box">

            <div class="login-logo">

                <!--<a><b style="color:#f3f3f3;"><?php echo $systemname; ?></b></a>-->

            </div>

            <!-- /.login-logo -->

            <div class="login-box-body">
                <?php if($_GET['forgot']=='yes'){ ?>
                <div style="display: block;margin: auto;"><img style="width: 65%;margin: auto;display: block;"
                        src="img/Religare-Logo-290x71.png"></div>
                <p class="login-box-msg" style="margin-bottom: 20px;">Forgot Password</p>
                <p style="text-align:center;color:red;"><?php echo $userforgot['Message']; ?></p>
                <form method="post" action="">

                    <div class="input-icons">
                        <i class="fa fa-user fa-2x icon"></i>
                        <input style="padding: 8px !important;  text-align: left !important;" class="input-field bb" type="text" name="forgotuserid" placeholder="Enter User Id...">
                    </div>

                    <div style="margin-top: 35px;">
                        <div style="display: initial;">
                            <div style=" display:inline-flex;">

                            </div>

                            <div style="float: right;">
                                <a href="login.php" style="color:#51de22;font-weight: 600;font-size: 17px;"> Go Back to
                                    Login</a>
                            </div>

                        </div>

                    </div>
                    <div class="row">

                        <div class="col-xs-12">

                            <button type="submit" name="forgotPassword" class="btn"
                                style="width: 224px;display: block;margin: 10px auto 0px;background:#51de22;color:#fff;font-size: 19px;font-weight: bold;">Submit</button>

                        </div>

                        <!-- /.col -->

                    </div>

                </form>


                <?php
  }else{ ?>

                <div style="display: block;margin: auto;"><img style="width: 65%;margin: auto;display: block;"
                        src="img/Religare-Logo-290x71.png"></div>
                <p class="login-box-msg" style="margin-bottom: 20px;">Login to your account</p>
                Now you are on new TINFC domain: <span style="color:blue;"> tinexpert.religaredigital.in </span>
                <p style="text-align:center;color:red;"><?php echo $userlogin; ?></p>
                <form method="post" onsubmit = "encryptCredentials()">

                    <div class="input-icons">
                        <i class="fa fa-user fa-2x icon"></i>
                        <input style="margin-bottom: 41px;" class="input-field bb" type="text" name="userId" id="userId"
                            placeholder="User Id">
                        <i class="fa fa-lock fa-2x icon"></i>
                        <input class="input-field bb" type="password" name="password" id="password" placeholder="Password">

                    </div>

                    <style>
                    input.larger {

                        width: 18px;
                        height: 19px;
                        margin-right: 6px;
                    }
                    </style>
                    <div style="margin-top: 35px;">
                        <div style="display: initial;">
                            <div style=" display:inline-flex;">
                                <input class="larger" type="checkbox" name="remember" id="remember">
                                <p style="color: #6b6a6afa;font-weight: 600;font-size: 17px;">Remember Me</p>
                            </div>

                            <div style="float: right;">
                                <a href="login.php?forgot=yes" style="color:#51de22;font-weight: 600;font-size: 17px;">
                                    Forgot Password</a>
                            </div>

                        </div>

                    </div>
                    <div class="row">

                        <div class="col-xs-12">

                            <button type="submit" name="userLogin" class="btn"
                                style="width: 224px;display: block;margin: 10px auto 0px;background:#51de22;color:#fff;font-size: 19px;font-weight: bold;">Submit</button>

                        </div>

                        <!-- /.col -->

                    </div>

                </form>

                <?php
  }
  ?>




                <!-- /.social-auth-links -->








            </div>

            <!-- /.login-box-body -->

        </div>

        <!-- /.login-box -->

        <!-- <div style="width: 100%;position: absolute;bottom: 0;background: white;text-align: right;padding-right:45px"><h5>@ <?php echo date('Y'); ?> Powered By <img src="images/deboxlogo.png" ></h5></div>
 -->

        <!-- jQuery 3 -->

        <!-- <script src="bower_components/jquery/dist/jquery.min.js"></script> -->

        <!-- Bootstrap 3.3.7 -->

      <!--  <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script> -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
		<script>

		function encode(str) {
var encoded = "";
str = btoa(str);
str = btoa(str);
for (i=0; i<str.length;i++) {
var a = str.charCodeAt(i);
var b = a ^ 10; // bitwise XOR with any number, e.g. 123
encoded = encoded+String.fromCharCode(b);
}
encoded = btoa(encoded);
return encoded;
}


function encryptCredentials111() {
    var userIdF = document.getElementById('userId').value;
	var passwordF = document.getElementById("password").value;
	var useridValue = encode(userIdF);
	var passwordValue = encode(passwordF);

    document.getElementById("userId").value = useridValue;
	 document.getElementById("password").value = passwordValue;
	return false;

}

function encryptCredentials() {
                var userIdF = document.getElementById('userId').value;
                var passwordF = document.getElementById("password").value;
                var useridValue = encode(userIdF);
                var passwordValue = encode(passwordF);
                $.ajax({
                    url: 'ajaxencrypt.php',
                    type: 'POST',
                    async: false,
                    data: {
                        userId: useridValue,
                        password: passwordValue
                    },
                    dataType: "json",
                    success: function(response) {
                       // console.log('sdfsfsdfsdf');
                        //console.log(response.useraes);
                       // console.log(response.passwordaes);
                        $('#userId').val(response.useraes);
                        $('#password').val(response.passwordaes);
                        console.log("Encrypted response received");
                        return true;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error occurred: " + error);
                    }
                });
                // Return false to prevent form submission
                return false;
            }
</script>

        <!-- iCheck -->

        <!-- <script src="plugins/iCheck/icheck.min.js"></script>
 -->
        <!-- <script>

  $(function () {

    $('input').iCheck({

      checkboxClass: 'icheckbox_square-blue',

      radioClass: 'iradio_square-blue',

      increaseArea: '20%' /* optional */

    });

  });

</script> -->
    </div>
</body>
