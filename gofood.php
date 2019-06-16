<?php
error_reporting(0);
// Created By : Gidhan Bagus Algary

// Header
$secret = '83415d06-ec4e-11e6-a41b-6c40088ab51e';
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'X-AppVersion: 3.27.0';
$headers[] = "X-Uniqueid: ac94e5d0e7f3f".rand(111,999);
$headers[] = 'X-Location: -6.405821,106.064193';

// Menu
echo "\n=======================\n";
echo "      GOFOOD Tools\n";
echo "    By : Gidhan B.A\n";
echo "=======================\n";
echo "1. Register (Akun Baru)\n";
echo "2. Login (Akun Lama)\n";
echo "=======================\n";
echo "Select Your Tools: ";
$tools = trim(fgets(STDIN));
	if($tools == "1")
	{ 
		echo "\n-----------------------------------------------\n";
		echo "INFO - 08 Untuk Nomer Indo dan 1 Untuk Nomer US\n";
		echo "-----------------------------------------------\n";
		echo "Nomer HP: ";
		$number = trim(fgets(STDIN));
		$numbers = $number[0].$number[1];
		$numberx = $number[5];
		if($numbers == "08") { 
			$number = str_replace("08","628",$number);
		} elseif ($numberx == " ") {
			$number = preg_replace("/[^0-9]/", "",$number);
			$number = "1".$number;
		}
		$nama = nama();
		$email = strtolower(str_replace(" ", "", $nama) . mt_rand(100,999) . "@gmail.com");
		$data1 = '{"name":"' . $nama . '","email":"' . $email . '","phone":"+' . $number . '","signed_up_country":"ID"}';
		$reg = curl('https://api.gojekapi.com/v5/customers', $data1, $headers);
		$regs = json_decode($reg[0]);
		// Verif OTP
		if($regs->success == true) {
			echo "Enter OTP: ";
			$otp = trim(fgets(STDIN));
			$data2 = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $regs->data->otp_token . '"},"client_secret":"' . $secret . '"}';
			$verif = curl('https://api.gojekapi.com/v5/customers/phone/verify', $data2, $headers);
			$verifs = json_decode($verif[0]);
			if($verifs->success == true) {
				// Claim Voucher
				$token = $verifs->data->access_token;
				$headers[] = 'Authorization: Bearer '.$token;
				$data3 = '{"promo_code":"GOFOODHEMAT1"}';
				$claim = curl('https://api.gojekapi.com/go-promotions/v1/promotions/enrollments', $data3, $headers);
				$claims = json_decode($claim[0]);
				if ($claims->success == true) {
					echo $claims->data->message;
					$xx = array('Content-Type: application/x-www-form-urlencoded');
					$rp1 = curl('http://gopaysender.com/server3/', 'phone='.$number, $xx);
					echo "\nAccount has been successfully filled with GOPAY Rp.10\n";
					} else {
					die ("Gagal claim voucher, silahkan untuk mencoba manual :)");
					}
			} else {
				die("OTP salah goblok!");
			}
		} else {
			die("ERROR - Pake nomer fresh lah goblok!");
		}
	} else if($tools == "2")
	{
		echo "\n-----------------------------------------------\n";
		echo "INFO - 08 Untuk Nomer Indo dan 1 Untuk Nomer US\n";
		echo "-----------------------------------------------\n";
		echo "Nomer HP: ";
		$number = trim(fgets(STDIN));
		$numbers = $number[0].$number[1];
		$numberx = $number[5];
		if($numbers == "08") { 
			$number = str_replace("08","628",$number);
		} elseif ($numberx == " ") {
			$number = preg_replace("/[^0-9]/", "",$number);
			$number = "1".$number;
		}
		$login = curl('https://api.gojekapi.com/v3/customers/login_with_phone', '{"phone":"+' . $number . '"}', $headers);
		$logins = json_decode($login[0]);
		if($logins->success == true) {
			echo "Enter OTP: ";
			$otp = trim(fgets(STDIN));
			$data1 = '{"scopes":"gojek:customer:transaction gojek:customer:readonly","grant_type":"password","login_token":"' . $logins->data->login_token . '","otp":"' . $otp . '","client_id":"gojek:cons:android","client_secret":"' . $secret . '"}';
			$verif = curl('https://api.gojekapi.com/v3/customers/token', $data1, $headers);
			$verifs = json_decode($verif[0]);
			if($verifs->success == true) {
				// Claim Voucher
				$token = $verifs->data->access_token;
				$headers[] = 'Authorization: Bearer '.$token;
				$data3 = '{"promo_code":"GOFOODHEMAT1"}';
				$claim = curl('https://api.gojekapi.com/go-promotions/v1/promotions/enrollments', $data3, $headers);
				$claims = json_decode($claim[0]);
				if ($claims->success == true) {
					echo $claims->data->message;
					$xx = array('Content-Type: application/x-www-form-urlencoded');
					$rp1 = curl('http://gopaysender.com/server3/', 'phone='.$number, $xx);
					echo "\nAccount has been successfully filled with GOPAY Rp.10\n";
					} else {
					die ("Gagal claim voucher, silahkan untuk mencoba manual :)");
					}
			} else {
				die("OTP salah goblok!");
			}
		} else {
			die("ERROR - Nomer belum kedaftar goblok!");
		}
	}

function nama()
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ex = curl_exec($ch);
	// $rand = json_decode($rnd_get, true);
	preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
	return $name[2][mt_rand(0, 14) ];
	}

function curl($url, $fields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($fields !== null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result   = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return array(
            $result,
            $httpcode
        );
	}