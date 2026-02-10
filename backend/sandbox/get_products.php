<?php
header('Content-Type: application/json; charset=utf-8');

$api_key = 'e888b918-330e-43c5-a103-111d57a4a28f';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://remotehost.es/student024/Shop/backend/endpoints/sellers/sellers_products.php?apikey=" . $api_key);
curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

$result = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($result === false) {
	http_response_code(502);
	echo json_encode(['success' => false, 'message' => 'Error contacting remote service', 'error' => $curlErr]);
	exit;
}

$decoded = json_decode($result, true);
// if (json_last_error() !== JSON_ERROR_NONE) {
// 	// Remote returned non-JSON (HTML/error page). Return as failure but include raw for debugging.
// 	http_response_code(502);
// 	echo json_encode(['success' => false, 'message' => 'Remote returned invalid JSON', 'http_code' => $httpCode, 'raw' => $result]);
// 	exit;
// }

// Wrap successful remote response
echo json_encode(['success' => true, 'http_code' => $httpCode, 'data' => $result]);