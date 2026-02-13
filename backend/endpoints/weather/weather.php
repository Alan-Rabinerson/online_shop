<?php
include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_log.php';
include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_logJSON.php';
            

$url_current = 'https://dataservice.accuweather.com/currentconditions/v1/305482?apikey=zpka_ae500aff75d84318aaa85c64a4594ab7_6393cd36&details=true&metric=true';

$url_forecast = 'https://dataservice.accuweather.com/forecasts/v1/daily/5day/305482?apikey=zpka_ae500aff75d84318aaa85c64a4594ab7_6393cd36&details=true&metric=true';
$json_data_current = file_get_contents($url_current);
$data_current = json_decode($json_data_current, true);
$json_data_forecast = file_get_contents($url_forecast);
$data_forecast = json_decode($json_data_forecast, true);
                
if ($data_current === null || $data_forecast === null) {
    write_log("Error al decodificar JSON de AccuWeather");
    echo json_encode(['error' => 'Error al obtener datos del clima']);
    exit;
}

$json_pretty_current = json_encode($data_current, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$json_pretty_current_db = mysqli_real_escape_string($conn, $json_pretty_current);
$json_pretty_forecast = json_encode($data_forecast, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$json_pretty_forecast_db = mysqli_real_escape_string($conn, $json_pretty_forecast);

$db_array =json_encode( [
    'current' => $json_pretty_current_db,
    'forecast' => $json_pretty_forecast_db
]);

$sql = "INSERT INTO 024_weather_data (cc_info_JSON, date_time) VALUES ('$db_array', NOW())";;
if (mysqli_query($conn, $sql)) {
    $file = fopen($_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/logs/current_weather_entry.json', 'w');
    fwrite($file, " "); // Vaciar el archivo antes de escribir
    fwrite($file, $json_data_current);
    fclose($file);
    $file_forecast = fopen($_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/logs/forecast_weather_entry.json', 'w');// Abrir en modo 'w' para sobrescribir
    fwrite($file_forecast, " "); // Vaciar el archivo antes de escribir
    fwrite($file_forecast, $json_data_forecast);
    fclose($file_forecast);
    write_logJSON("saved new entry for weather data ",'insert', 'weather', 'changes_log.json');
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/logs/weather_log.json', $json_data_current);
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/logs/weather_log.json', $json_data_forecast);
    
    // Devolver los datos del clima en formato JSON
    header('Content-Type: application/json');
    echo json_encode([
        'WeatherText' => $data_current[0]['WeatherText'] ?? 'N/A',
        'Temperature' => $data_current[0]['Temperature']['Metric']['Value'] ?? 'N/A',
        'WeatherIcon' => $data_current[0]['WeatherIcon'] ?? 1,
        'Forecast' => $data_forecast['DailyForecasts'] ?? []
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al guardar datos del clima en la base de datos: ' . mysqli_error($conn)]);
}
mysqli_close($conn);