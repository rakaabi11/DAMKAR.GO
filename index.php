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


// Buat Instance baru
$app = new Visualstudio();

// Baca query dari alamat
$app->query_string = $_SERVER['QUERY_STRING'];

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
                $targetradius_pemuda = new LatLong(-6.190374,106.879389);
                $targetradius_UNJ = new LatLong(-6.1944197,106.8788192);
                $targetradius_petukangan = new LatLong(-6.249227,106.753435);
                $targetradius_condet = new LatLong(-6.299491,106.865473);

                $dataLoc = $app->read_data_wilayah();

                // Get the distance between these two Lat/Long coordinates...
                $distance_UNJ = new DistanceCalculator($lokasiKebakaran, $targetradius_UNJ);
                $distance_petukangan = new DistanceCalculator($lokasiKebakaran, $targetradius_petukangan);
                $distance_condet = new DistanceCalculator($lokasiKebakaran, $targetradius_condet);
                $distance_pemuda = new DistanceCalculator($lokasiKebakaran, $targetradius_pemuda);

                $distance_UNJ = $distance_UNJ ->get();
                $distance_petukangan = $distance_petukangan->get();
                $distance_condet = $distance_condet->get();
                $distance_pemuda = $distance_pemuda->get();

                echo 'Jarak kebakaran is: ' . $distance_UNJ->asKilometres();
                echo 'Jarak kebakaran is: ' . $distance_petukangan->asKilometres();
                echo 'Jarak kebakaran is: ' . $distance_condet->asKilometres();
                echo 'Jarak kebakaran is: ' . $distance_pemuda->asKilometres();
               

              $username = "root";
              $password = "";
              $database = "db_damkar";
              $mysqli = mysqli_connect('localhost', $username, $password, $database);

              $AreaLuas= "Luas" ;
              $AreaSempit ="Sempit";

              $Total_kebakaran_Rawamangun ="172";
              $Total_kebakaran_Pesanggrahan="108";
              $Total_kebakaran_Pasarrebo="89";

              $Total_Hydrant_UNJ ="12";
              $Total_Hydrant_petukangan="1";
              $Total_Hydrant_condet="20";
              $Total_Hydrant_pemuda="0";
              
              $sql= "INSERT INTO wilayah_radius (Area, Total_kebakaran, Hydrant) VALUES 
              ($AreaLuas, $Total_kebakaran_Rawamangun, $Total_Hydrant_UNJ),
              ($AreaLuas, $Total_kebakaran_Pesanggrahan, $Total_Hydrant_petukangan),
              ($AreaSempit, $Total_kebakaran_Pasarrebo, $Total_Hydrant_condet),
              ($AreaSempit, $Total_kebakaran_Rawamangun, $Total_Hydrant_pemuda)";
              if($distance_UNJ->asKilometres()< 0.5){
                    
                    echo "id: 1 "."<br>"; 
                    echo " Total kebakaran : $Total_kebakaran_Rawamangun"."<br>";
                    echo " Area: $AreaLuas"."<br>"; 
                    echo " Total Hydrant: $Total_Hydrant_UNJ" ."<br>";
                    
                }
                if($distance_petukangan->asKilometres()< 2.0){
                    echo "id: 2 "."<br>";
                    echo " Total kebakaran :$Total_kebakaran_Pesanggrahan"."<br>";
                    echo " Area: $AreaLuas"."<br>"; 
                    echo " Total Hydrant: $Total_Hydrant_petukangan" ."<br>";
                }
                if($distance_condet->asKilometres()< 2.0){
                    echo "id: 3 "."<br>";
                    echo " Total kebakaran :$Total_kebakaran_Pasarrebo"."<br>";
                    echo " Area: $AreaSempit"."<br>"; 
                    echo " Total Hydrant: $Total_Hydrant_condet" ."<br>";
                }
                if($distance_pemuda->asKilometres()< 0.5){
                    echo "id: 4 "."<br>";
                    echo " Total kebakaran :$Total_kebakaran_Rawamangun"."<br>";
                    echo " Area: $AreaSempit"."<br>"; 
                    echo " Total Hydrant: $Total_Hydrant_pemuda" ."<br>";
                }
            
                
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
