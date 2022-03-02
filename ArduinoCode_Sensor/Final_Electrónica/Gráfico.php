<?php
$servername = "localhost";
// REPLACE with your Database name
$dbname = "id17202099_bdsensor";
// REPLACE with Database user
$username = "id17202099_christye";
// REPLACE with Database user password
$password = "oI}3mEi/wwoZ=Dn%";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, value1, value2, value3, reading_time FROM SensorData order by reading_time desc limit 40";
$result = $conn->query($sql) or die($conn->error);
while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}
$readings_time = array_column($sensor_data, 'reading_time');
$i = 0;
foreach ($readings_time as $reading){
    $readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading + 7 hours")); //convert timezone to +7
    $i += 1;
}
$value1 = json_encode(array_reverse(array_column($sensor_data, 'value1')), JSON_NUMERIC_CHECK);
$value2 = json_encode(array_reverse(array_column($sensor_data, 'value2')), JSON_NUMERIC_CHECK);
$value3 = json_encode(array_reverse(array_column($sensor_data, 'value3')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);
/*echo $value1;
echo $value2;
echo $value3;
echo $reading_time;*/
$result->free();
$conn->close();
$jSon = "
{
  chart: { renderTo : 'chart-temperature' },
  title: { text: 'Temperatura BMP180' },
  series: [{
     showInLegend: false,
     data: value1
  }],
  plotOptions: {
     line: { animation: false,
         dataLabels: { enabled: true }
     },
     series: { color: '#059e8a' }
  },
  xAxis: {
     type: 'datetime',
     categories: reading_time
  },
  yAxis: {
     title: { text: 'Temperatura (Celsius)' }

  },
  credits: { enabled: false }
}";
echo json_decode($jSon);

$jSon2 = "
chart:{ renderTo:'chart-pressure' },
title: { text: 'Presión BMP180' },
series: [{
  showInLegend: false,
  data: value2
}],
plotOptions: {
  line: { animation: false,
    dataLabels: { enabled: true }
  }
},
xAxis: {
  type: 'datetime',

  categories: reading_time
},
yAxis: {
  title: { text: 'Presión (Pa)' }
},
credits: { enabled: false }
"

$jSon3 = "
chart:{ renderTo:'chart-approximate-altitude' },
title: { text: 'Altitud aproximada BMP180' },
series: [{
  showInLegend: false,
  data: value3
}],
plotOptions: {
  line: { animation: false,
    dataLabels: { enabled: true }
  },
  series: { color: '#18009c' }
},
xAxis: {
  type: 'datetime',
  categories: reading_time
},
yAxis: {
  title: { text: 'Altitud Aproximada (m)' }
},
credits: { enabled: false }
"
?>
