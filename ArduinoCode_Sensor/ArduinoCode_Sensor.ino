 #include <WiFi.h>
#include <HTTPClient.h>
#include <WebServer.h>
#include <Adafruit_BMP085.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BMP280.h>

#include <SFE_BMP180.h>
#include <Wire.h>
// Replace with your network credentials
const char* ssid     = "MOVISTAR_1868";
const char* password = "9FMJBREKHhTVCYTCAV3K";
// REPLACE with your Domain name and URL path or IP address with path
const char* serverName = "http://w86swxfws4ubazvscs.000webhostapp.com/POST_DATA.php";
// Keep this API Key value to be compatible with the PHP code provided in the project page. 
// If you change the apiKeyValue value, the PHP file /post-esp-data.php also needs to have the same key 
String apiKeyValue = "tPmAT5Ab3j7F9";
String sensorName = "BMP280";
String sensorLocation = "Office";
#define SEALEVELPRESSURE_HPA (1013.25)

Adafruit_BMP085 bmp;// I2C
//Adafruit_BMP280 bmp(BMP_CS); // hardware SPI
//Adafruit_BMP280 bmp(BMP_CS, BMP_MOSI, BMP_MISO,  BMP_SCK);
void setup() {
  Serial.begin(115200);
  
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
// (you can also pass in a Wire library object like &Wire2)
  bool status = bmp.begin(0x76);
  if (!status) {
    Serial.println("Could not find a valid BMP280 sensor, check wiring or change I2C address!");
    while (1);
  }
}
void loop() {
  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    HTTPClient http;
    
    // Your Domain name with URL path or IP address with path
    http.begin(serverName);
    
    // Specify content-type header
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // Prepare your HTTP POST request data
    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                          + "&location=" + sensorLocation + "&value1=" + String(bmp.readTemperature())
                          + "&value2=" + String(bmp.readAltitude()) + "&value3=" + String(bmp.readPressure()/100.0F) + "";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);
    
    // You can comment the httpRequestData variable above
    // then, use the httpRequestData variable below (for testing purposes without the BMP280 sensor)
    //String httpRequestData = "api_key=tPmAT5Ab3j7F9&sensor=BMP280&location=Office&value1=24.75&value2=49.54&value3=1005.14";
// Send HTTP POST request
    int httpResponseCode = http.POST(httpRequestData);
     
   
        
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    // Free resources
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  //Send an HTTP POST request every 30 seconds (30.000 mili seconds)
  delay(10000);  
}
