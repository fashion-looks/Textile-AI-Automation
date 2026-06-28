<?php
function findImageScale($source_file){

$im = ImageCreateFromJpeg($source_file);
$imgw = imagesx($im);
$imgh = imagesy($im);
$r = array();
$g = array();
$b = array();
$c = 0;
    for ($i=0; $i<$imgw; $i++)
    {
        for ($j=0; $j<$imgh; $j++)
        {
       		// get the rgb value for current pixel
            $rgb = ImageColorAt($im, $i, $j);
            // extract each value for r, g, b
            $r[$i][$j] = ($rgb >> 16) & 0xFF;
            $g[$i][$j] = ($rgb >> 8) & 0xFF;
            $b[$i][$j] = $rgb & 0xFF;
            // count gray pixels (r=g=b)
            if ($r[$i][$j] == $g[$i][$j] && $r[$i][$j] == $b[$i][$j])
            {
                $c++;
            }
        }
    }

    if ($c == ($imgw*$imgh))
    {
        return 'no';
    }else
    {
        return 'yes';
    }
}

function logger($errorlog)
{
	$newfile = 	'errorlog/Debuglog_'.date('dmy').'.txt';

	//rename('errorlog/miserrorlog.txt',$newfile);

	if(!file_exists($newfile))
	{
	  file_put_contents($newfile,'');
	}
	$logfile=fopen($newfile,'a');


	$ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('Asia/Kolkata');
	$time = date('d-m-Y h:i:s A',time());
	//$contents = file_get_contents('errorlog/errorlog.txt');
	$contents = "$ip\t$time\t$errorlog\r";
	fwrite($logfile,$contents);
	//file_put_contents('errorlog/errorlog.txt',$contents);
}

function createZip($zip,$dir){
    if (is_dir($dir)){

        if ($dh = opendir($dir)){
            while (($file = readdir($dh)) !== false){

                // If file
                if (is_file($dir.$file)) {
                    if($file != '' && $file != '.' && $file != '..'){

                        $zip->addFile($dir.$file);
                    }
                }else{
                    // If directory
                    if(is_dir($dir.$file) ){

                        if($file != '' && $file != '.' && $file != '..'){

                            // Add empty directory
                            $zip->addEmptyDir($dir.$file);

                            $folder = $dir.$file.'/';

                            // Read data of the folder
                            createZip($zip,$folder);
                        }
                    }

                }

            }
            closedir($dh);
        }
    }
}

function delete_directory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
     if (!$dir_handle)
          return false;
     while($file = readdir($dir_handle)) {
           if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                     unlink($dirname."/".$file);
                else
                     delete_directory($dirname.'/'.$file);
           }
     }
     closedir($dir_handle);
     rmdir($dirname);
     return true;
}

function dateFormat($d){
     $returndate = '';
     if($d=='01011970' || $d=='' || $d=='1970-01-01'){
         return $returndate;
     }else{
        //$date = substr($d,0,2).'-';
        //$month = substr($d,2,2).'-';
        //$year = substr($d,4,5);
        //$returndate = $date.$month.$year;
		$date = DateTime::createFromFormat('dmY', $d);
        $returndate = $date->format('d-m-Y');
        return $returndate;
     }

}

function dateFormatAll($d){
     $returndate = '';
     if($d=='01011970' || $d=='' || $d=='1970-01-01'){
         return $returndate;
     }else{
        $returndate = date('d-m-Y',strtotime($d));
        return $returndate;
     }

}

function getCurlImage($imagepath){
		$chr = curl_init();
		curl_setopt($chr, CURLOPT_URL, $imagepath);
		curl_setopt($chr, CURLOPT_HEADER, 0);
		curl_setopt($chr, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chr, CURLOPT_BINARYTRANSFER,1);
		curl_setopt($chr, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($chr, CURLOPT_SSL_VERIFYHOST, false);
		$picture = curl_exec($chr);
		curl_close($chr);
		return $picture;
}

function getCurlData($geturl){
		logger("GET API URL HIT: ".$geturl);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $geturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
}

function postCurlData($posturl,$jsondata){
		logger("POST API URL HIT: ".$posturl);
		logger("JSON POST : ".$jsondata);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$posturl);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
}

function funcGetFileContent($imagepath){
		$picture = file_get_contents($imagepath);
		return $picture;
}

function encode($str){
	$encodeString = base64_encode(htmlspecialchars($str));
	return $encodeString;
}


function decode($str){
	$encodeString = base64_decode($str);
	return $encodeString;
}

function logger2($errorlog)
{
	$newfile = 	'errorlog/Debuglog2_'.date('dmy').'.txt';

	//rename('errorlog/miserrorlog.txt',$newfile);

	if(!file_exists($newfile))
	{
	  file_put_contents($newfile,'');
	}
	$logfile=fopen($newfile,'a');


	$ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('Asia/Kolkata');
	$time = date('d-m-Y h:i:s A',time());
	//$contents = file_get_contents('errorlog/errorlog.txt');
	$contents = "$ip\t$time\t$errorlog\r";
	fwrite($logfile,$contents);
	//file_put_contents('errorlog/errorlog.txt',$contents);
}

function clean($str){
	$cleanString = addslashes(trim($str));
	return $cleanString;
}

function getUserType($str){
	if($str=="QCP"){
        return "QC";
    }
    elseif($str=="QCF"){
        return "QC Fulfillment User";
    }
    elseif($str=="HOUSER"){
        return "HO User";
    }
    elseif($str=="BCP"){
        return "Batch User";
    }
    elseif($str=="NSD"){
        return "Response User";
    }
    elseif($str=="VENDOR"){
        return "Vendor";
    }
    elseif($str=="BACKHO"){
        return "Back HO";
    }
    elseif($str=="SUPER"){
        return "Admin";
    }
    elseif($str=="BRANCH"){
        return "Branch";
    }
	else{
        return '';
    }
}

/* sso code start */
function encryptData($plaintext, $key, $iv) {
    return base64_encode(openssl_encrypt($plaintext, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv));
}


// Decrypt the response
function decryptData($ciphertext, $key, $iv) {
    $decrypted = openssl_decrypt(base64_decode($ciphertext), "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
    if (false === $decrypted) {
        throw new Exception('Decryption failed: ' . openssl_error_string());
    }
    return $decrypted;
}

// Function to send encrypted request to the login API
function sendLoginRequest($data, $key, $iv, $url) {
    $encryptedData = encryptData($data, $key, $iv);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['data' => $encryptedData]));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'appid: ' . json_decode($data, true)['appId']
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        throw new Exception(curl_error($curl));
    }
    curl_close($curl);
    return $response;
}


function handleResponse($encryptedResponse, $key, $iv) {
    $response = json_decode($encryptedResponse, true);
    if (!$response) {
        throw new Exception('Invalid JSON response');
    }
    $decryptedRes = decryptData($response['data'], $key, $iv);
    return json_decode($decryptedRes, true);
}



function sendPostRequest($apiUrlValidate, $validateRequest) {
    // Initialize cURL session
    $ch = curl_init($apiUrlValidate);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $validateRequest);

    // Execute cURL session
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        // Decode the JSON response
      
       return $response_data = json_decode($response, true);
        
        // Simulate or check for a specific response code (for demonstration)
       // $response_data['code'] = 'SC001';  // This line should typically not be here, it should come from the response itself.

        // Check the API response
       
    }

    // Close cURL session
    curl_close($ch);
}
/* sso code end */

?>
