#ifdef ESP32
  #include <WiFi.h>
  #include <HTTPClient.h>
#else
  #include <ESP8266WiFi.h>
  #include <ESP8266HTTPClient.h>
  #include <WiFiClient.h>
#endif
#include <ArduinoJson.h>;
// Replace with your network credentials
const char* ssid     = "Familia Sosa";
const char* password = "Sosas159357";
const int ledPin = 23;
// REPLACE with your Domain name and URL path or IP address with path
const char* serverName = "https://proyectosanseviera.edu.pe/controller/datos_consulta.php?id=1";

// Keep this API Key value to be compatible with the PHP code provided in the project page. 
// If you change the apiKeyValue value, the PHP file /post-data.php also needs to have the same key 
String id = "1";

void setup() {
  Serial.begin(115200);
  pinMode(ledPin, OUTPUT);
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());

}

void loop() {
  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    HTTPClient http;
    
    // Your Domain name with URL path or IP address with path
    http.begin(serverName);
    
    http.addHeader("Content-Type", "application/json");
    int httpResponseCode = http.GET(); //Send the request
    
    
    if (httpResponseCode>0) {
    String payload = http.getString(); //Get the request response payload
      char json[500];
      payload.replace(" ", "");
      payload.replace("\n", "");
      payload.trim();
      payload.remove(0,1);
      payload.toCharArray(json,500);
    StaticJsonDocument<200> doc;
    deserializeJson(doc, json);
    int Estado=doc["Estado"];
    Serial.println(payload); //Print the response payload
      if(Estado==1)
      {digitalWrite(ledPin, HIGH); }
      else
      {digitalWrite(ledPin, LOW); }
       
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
  //Send an HTTP POST request every 30 seconds
  delay(3000);  
}
