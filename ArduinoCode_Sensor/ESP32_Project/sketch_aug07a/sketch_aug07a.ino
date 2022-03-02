#include <ESP8266WiFi.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <WebServer.h>
#include <Wire.h>

//Reemplazar con los valores de tu red
const char* ssid     = "Noombre de tu red";
const char* password = "Contraseña de tu res";

// Designamos el web server en el puerto 80
WiFiServer server(80);

// Variable para HTTP
String header;

// Variables auxiliares para ver el estado del pin de salida
String output5State = "off";

// Creamos una variable y la relacionamos al pin 5
const int output5 = 5;

void setup() {
  Serial.begin(115200);
  // Configuramos los modos del pin de salida
  pinMode(output5, OUTPUT);
  // Declaramos que inice en LOW o Bajo
  digitalWrite(output5, LOW);

  // Connect to Wi-Fi network with SSID and password
  Serial.print("Conectando a: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);            // Configura los datos de la red
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  // Imprimir datos del webserver
  Serial.println("");
  Serial.println("WiFi conectado.");
  Serial.println("Dirección IP: ");
  Serial.println(WiFi.localIP());    // Wifi.localIP() muestra la IP del esp8266
  server.begin();                    // Inicias el servidor
}

void loop(){
  WiFiClient client = server.available();   // Listen for incoming clients

  if (client) {                             // Si un nuevo cliente se conecta,
    Serial.println("Nuevo cliente.");          // se imprime un mensaje en el puerto serial
    String currentLine = "";                // hacer una cadena para contener los datos entrantes del cliente
    while (client.connected()) {            // función repetitiva mientras el cliente este conectado
      if (client.available()) {             // Si hay bytes para leer desde el cliente,
        char c = client.read();             // lee un byte, entonces
        Serial.write(c);                    // lo impríme en el monitor serial
        header += c;
        if (c == '\n') {                    // if the byte is a newline character
          // Si la línea actual está en blanco, tienes dos caracteres de nueva línea seguidos
          // Ese es el final de la solicitud HTTP del cliente, así que envíe una respuesta:
          if (currentLine.length() == 0) {
            // La cabecera de HTTP inica con el siguiente código (e.g. HTTP/1.1 200 OK)
            // Y en content-type el cliente sabe el tipo de contenido que hay:
            client.println("HTTP/1.1 200 OK");
            client.println("Content-type:text/html");
            client.println("Connection: close");
            client.println();
            
            // turns the GPIOs on and off
            if (header.indexOf("GET /5/on") >= 0) {
              Serial.println("GPIO 5 on");
              output5State = "on";
              digitalWrite(output5, HIGH);
            } else if (header.indexOf("GET /5/off") >= 0) {
              Serial.println("GPIO 5 off");
              output5State = "off";
              digitalWrite(output5, LOW);
            }
            
            // Mostrar la página HTML
            client.println("<!DOCTYPE html><html>");
            client.println("<head><meta http-equiv=\"refresh\" content=\"2\" name=\"viewport\" content=\"width=device-width, initial-scale=1\">");
            client.println("<link rel=\"icon\" href=\"data:,\">");
            // Estilos CSS de los botones on/off 
            // Puedes modificar estas carcterísticas a tu gusto
            client.println("<style>html { font-family: Helvetica; display: inline-block; margin: 0px auto; text-align: center;}");
            client.println(".button { background-color: #74e239; border: none; color: white; padding: 16px 40px;");
            client.println("text-decoration: none; font-size: 30px; margin: 2px; cursor: pointer;}");
            client.println(".button2 {background-color: #77878A;}</style></head>");
            
            // Cabecera de página
            client.println("<body><h1>SMELPRO - Web Server</h1>");
           // client.println("<p>Mi sensor es:" + valorsensor + "</p>");
            // Display current state, and ON/OFF buttons for GPIO 5  
            client.println("<p>Estado del ventilador: " + output5State + "</p>");
            // Si el pin está apagado (off) mostar el botón de encender (ON)      
            if (output5State=="off") {
              client.println("<p><a href=\"/5/on\"><button class=\"button\">ON</button></a></p>");
            } else {
              client.println("<p><a href=\"/5/off\"><button class=\"button button2\">OFF</button></a></p>");
            } 
            client.println("</body></html>");
            client.println();
            break;
          } else { // if you got a newline, then clear currentLine
            currentLine = "";
          }
        } else if (c != '\r') {
          currentLine += c;
        }
      }
    }
    // Limpiar variables de cabecera
    header = "";
    // Cerrar la conexión
    client.stop();
    Serial.println("Cliente desconectado.");
    Serial.println("");
  }
}
