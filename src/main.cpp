#include <Arduino.h>
#include <TinyGPSPlus.h>
#include <SoftwareSerial.h> //Libarary bawaan
#include <TimeLib.h>
#include <WiFi.h>
#include <NTPClient.h>
#include "HTTPClient.h"
#include <stdlib.h>
#include <esp32-hal-ledc.h>
#include <pwmWrite.h>


// Choose two Arduino pins to use for software serial
int RXPin = 16; //Connect ke TX GPS
int TXPin = 17; //Connect ke RX GPS
int buttonPin = 23; 
int ledPin = 2; // + LED
int Buzzer = 4;
boolean Buzzer_1 = true;

HardwareSerial neogps(1);
int GPSBaud = 9600; //Biarin default

int buttonState = 0;  // variable for reading the pushbutton status

const char* ssid = "RinaL1103";
const char* password = "LarantukaNTT1403";
const char* host = "192.168.100.29";
const int port = 80;
String url;


// Membuat objek TinyGPS++
TinyGPSPlus gps;

// Mmebuat koneksi serial dengan nama "gpsSerial"
SoftwareSerial gpsSerial(RXPin, TXPin);
unsigned long lastLocationTime = 0; // Waktu terakhir mendeteksi lokasi

WiFiUDP ntpUDP;
WiFiClient client;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 25200); // GMT+7

void setup()
{
  //Memulai koneksi serial pada baudrate 9600
  Serial.begin(9600);

  //Memulai koneksi serial dengan sensor
  gpsSerial.begin(GPSBaud);

// Set up Wi-Fi
  Serial.begin(9600);
  delay (1000);
 
  Serial.println();
  Serial.println();
  Serial.print("Connecting to : ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  // Print local IP address and start web server
  Serial.println("");
  Serial.println("WiFi connected.");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  timeClient.begin();
  timeClient.setTimeOffset(7 * 3600); // WIB is GMT+7
  timeClient.update();

  // initialize the LED pin as an output:
  pinMode(ledPin, OUTPUT);
  pinMode(Buzzer, OUTPUT);
  // initialize the pushbutton pin as an input:
  pinMode(buttonPin, INPUT);
}

void loop() {
  
  if (!client.connect(host, 80)) {
    Serial.print(".");
    delay(500);
  }
      Serial.println("");
      Serial.println("connected.");

  if (digitalRead(buttonPin) == HIGH) { // Jika push button ditekan
  boolean titik_lokasi = true;
  boolean Buzzer_1 = true;
  
   while (gpsSerial.available() > 0)  
    if (gps.encode(gpsSerial.read())){
      if (gps.location.isValid())
  {  

  HTTPClient http;
  url = "http://" + String(host) + "/PendeteksiLokasiKebakaran/Visualstudio/index.php?mode=save&titik_lokasi=" + String(gps.location.lat(), 6) + String (",") +String (gps.location.lng(), 6);
  http.begin(url);
  http.GET();
  //baca respon setelah berhasil kirim nilai sensor
  String respon = http.getString();
  Serial.println(respon);
 
  Serial.print("Latitude:");
  Serial.println(gps.location.lat(), 6);
  Serial.print("Longitude:");
  Serial.println(gps.location.lng(), 6);
  Serial.print("Altitude:");
  Serial.println(gps.altitude.meters());

  while (titik_lokasi=true)
  if(titik_lokasi=true , digitalRead(buttonPin) == HIGH){
  Serial.println("GPS not available");
  !titik_lokasi;
  gpsSerial.end();
  delay(1000);

  while (Buzzer_1=true)
  if(Buzzer_1=true, digitalRead(buttonPin) == HIGH){
  delay(5000);
  noTone(Buzzer);
  !Buzzer_1;
  delay(100);
  }
  else {
  if (digitalRead(buttonPin) == LOW){
    return;
  }
  }
  }
  else{ 
    if (digitalRead(buttonPin) == LOW){
    Serial.println("GPS MATI");
    return;
  }
  }

  // Set header Request
  client.print(String("GET ") + url + " HTTP/1.1\r\n" + "Latitude"+"Longitude" +
    "Host: " + host + "\r\n" +
    "Connection: close\r\n\r\n");
 // Pastikan tidak berlarut-larut
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 3000) {
      Serial.println(">>> Client Timeout !");
      Serial.println(">>> Operation failed !");
      client.stop();
      return;
    }
      // Baca hasil balasan dari PHP
    while (client.available()) {
    String line = client.readStringUntil('\r');
    Serial.println(line);
    }
    http.end();
    }
  }
  Serial.println();
  Serial.println();
  timeClient.update();
  Serial.print("Current time: ");
  Serial.println(timeClient.getFormattedTime());
  delay(1000);
  }
  // Jika dalam 5 detik tidak ada koneksi, maka akan muncul error "No GPS detected"
  // Periksa sambungan dan reset arduino
 if (millis() > 5000 && gps.charsProcessed() < 10)
  {
    Serial.println("No GPS detected");
    while(false);
  }

  if(titik_lokasi == false)
  {
  titik_lokasi = true;
  Serial.println(gps.satellites.value());
  }
  
  // check if the pushbutton is pressed. If it is, the buttonState is HIGH:
  // turn LED on:
  digitalWrite(ledPin, HIGH);
  tone(Buzzer,1000);

  } else {
  noTone(Buzzer);
  digitalWrite(ledPin, LOW);
  Serial.println("GPS MATI");
  delay(100);
  }
 }

  