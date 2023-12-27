<?php

//include library utama
include_once 'coders.php';
 

//PHP MAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once "library/PHPMailer.php";
include_once "library/Exception.php";
include_once "library/OAuth.php";
include_once "library/POP3.php";
include_once "library/SMTP.php";
include_once "library/distance/Distical.inc.php";

use Ballen\Distical\Calculator as DistanceCalculator;
use Ballen\Distical\Entities\LatLong;

// Set our Lat/Long coordinates
$ipswich = new LatLong(-6.1965316,106.8785516);
$kebakaran = new LatLong(-6.190374,106.879389);

// Get the distance between these two Lat/Long coordinates...
$distanceCalculator = new DistanceCalculator($ipswich, $kebakaran);

// You can then compute the distance...
$distance = $distanceCalculator->get();
// you can also chain these methods together eg. $distanceCalculator->get()->asMiles();

// We can now output the miles using the asMiles() method, you can also calculate and use asKilometres() or asNauticalMiles() as required!
echo 'Distance in miles between Central Ipswich and Jarak kebakaran is: ' . $distance->asKilometres();

// Buat Instance baru
$app = new Visualstudio();

// Baca query dari alamat
$app->query_string = $_SERVER['QUERY_STRING'];

// If search form is submitted 
if(isset($_POST['searchSubmit'])){ 
    if(!empty($_POST['location'])){ 
        $Alamat = $_POST['location']; 
    } 
    if(!empty($_POST['loc_latitude'])){ 
        $Latitude = $_POST['loc_Latitude']; 
    } 
    
    if(!empty($_POST['loc_longitude'])){ 
        $Longitude = $_POST['loc_Longitude']; 
    } 
     
    if(!empty($_POST['distance_km'])){ 
        $distance_km = $_POST['distance_km']; 
    } 
} 
// Calculate distance and filter records by radius 
$sql_distance = $having = ''; 
if(!empty($distance_km) && !empty($latitude) && !empty($longitude)){ 
    $radius_km = $distance_km; 
    $sql_distance = " ,(((acos(sin((".$latitude."*pi()/180)) * sin((`p`.`latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`p`.`latitude`*pi()/180)) * cos(((".$longitude."-`p`.`longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance "; 
    
    $having = " HAVING (distance <= $radius_km) "; 
    
    $order_by = ' distance ASC '; 
}else{ 
    $order_by = ' p.id DESC '; 
} 


// Cek apakah ada query bernama mode?
if ($app->is_url_query('mode')) {

    // Bagi menjadi beberapa operasi
    switch ($app->get_url_query_value('mode')) {

        default:
        $app->read_data();
        
        case 'save':
            if ($app->is_url_query('titik_lokasi')) {
                $titik_lokasi = $app->get_url_query_value('titik_lokasi');
                $app->create_data($titik_lokasi);
                
                $pesan = 'Terjadi kebakaran pada lokasi : https://maps.google.com/maps?q='.$titik_lokasi;
                $url = 'https://api.telegram.org/bot6606593581:AAFc9b-S-l8y1KnOlZhQUbEWymnhj9_zth0/sendMessage?chat_id=1472739327&text='.$pesan;
                $result=file_get_contents($url,true);

                $titikArr = explode(',', $titik_lokasi);

                $lat = (float)$titikArr[0];
                $long = (float)$titikArr[1];
                
                $lokasiKebakaran = new LatLong($lat,$long);
                $kebakaran = new LatLong(-6.1944197,106.8788192);
                
                $dataLoc = $app->read_data_wilayah();

                $arrLoc = [];

                foreach ($dataLoc as $x) {
                    $arr = [];
                    foreach($x as $y => $z){
                        if($y == "Latitude") {
                            array_push($arr,$z);
                        }
                        if($y == "Longitude") {
                            array_push($arr,$z);
                        }
                    }
                    array_push($arrLoc, $arr);
                }
                var_dump($arrLoc);
                  

                // Get the distance between these two Lat/Long coordinates...
                $distance1 = new DistanceCalculator($lokasiKebakaran, $kebakaran);
                $distance2 = $distance1->get();

                echo 'Jarak kebakaran is: ' . $distance2->asKilometres() . $dataLoc;
            
                
                $mail = new PHPMailer();
                //Server settings
                $mail->SMTPDebug = 1;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'ssl://smtp.gmail.com';                     //Set the SMTP server to send through                          //Enable SMTP authentication
                $mail->Username   = 'alatpendeteksilokasikebakaran@gmail.com';                     //SMTP username
                $mail->Password   = 'tubllfpebegkrnlj';                               //SMTP password           //Enable implicit TLS encryption
                $mail->Port       = 465;
                $mail->SMTPSecure = "ssl";
                $mail->SMTPAuth = true;
                
                //Recipients
                $mail->From = $mail->Username;
                $mail->FromName = "DAMKAR GO";
                $mail->addAddress('damkargo99@gmail.com');
                
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Kebakaran';
                $mail->Body    = 'Terjadi kebakaran pada lokasi <b>'.$titik_lokasi.'</b>';
                $error = $mail->send();
                echo $error;
                if(!$error){
                    echo $app->error_handler($mail->ErrorInfo);
                }
            } else {
                $error = [
                    'titik_lokasi' => 'required'
                ];
                echo $app->error_handler($error);
            }
            break;

        case 'save_waktu_padam':
            if ($app->is_url_query('id')) {
                $id = $app->get_url_query_value('id');
                $waktu_padam = new DateTime();
                $app->create_data_padam($id);
            } else {
                $error = [
                    'id' => 'required'
                ];
                echo $app->error_handler($error);
            }
            break;

        case 'delete':
            if ($app->is_url_query('id')) {
                $id = $app->get_url_query_value('id');
                $app->delete_data($id);
            } else {
                $error = [
                    'id' => 'required',
                ];
                echo $app->error_handler($error);
            }
            break;

        case 'update':
            if ($app->is_url_query('id')) {
                $id = $app->get_url_query_value('id');

                if ($app->is_url_query('titik_lokasi')) {
                    $titik_lokasi = $app->get_url_query_value('titik_lokasi');
                    $titik_lokasi = $app->get_url_query_value('latitude', 'longitude', 'altitude');
                    $app->update_data($id, $titik_lokasi);
                }
            } else {
                $error = [
                    'id' => 'required',
                    'titik_lokasi' => 'OR required',
                ];
                echo $app->error_handler($error);
            }
            break;
    }
} else {
    $app->read_data();
}
