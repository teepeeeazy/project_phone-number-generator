<?php
/**
 * Author: Thapelo Mokolo
 * Date: 2025-04-08
 * SmokeCI PHP Assesment
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    loadEnv(__DIR__ . '/.env');
    $quantity = intval($_POST['quantity']);
    $countryCode = htmlspecialchars($_POST['country_code']);

    if ($quantity <= 0) {
        die("Quantity must be a positive number.");
    }
    if (empty($countryCode)) {
        die("Country code is required.");
    }

    $phoneNumbers = [];
    for ($i = 0; $i < $quantity; $i++) {
        $length = rand(8, 10);
        $randomNumber = rand(pow(10, $length - 1), pow(10, $length) - 1);
    
        $phoneNumbers[] = "+$countryCode$randomNumber";
    }

    $data = ['phone_numbers' => $phoneNumbers];
    $url =  $_ENV['MICROSERVICE_URL'];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        $errorResponse = json_decode($response, true);
        $errorMessage = $errorResponse['error'] ?? 'Unknown error occurred.';
        
        die("Error communicating with the microservice. HTTP Code: $httpCode. Error: $errorMessage");
    }
    
    $result = json_decode($response, true);

    echo "<h1>Validation Results</h1>";
    echo "<p>Out of the " . $quantity . " numbers generated, " . $result['valid_count'] . " were valid.</p>";
    echo "<p>Percentage valid: " . $result['valid_percentage'] . "%</p>";
    
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Phone Number</th>";
    echo "<th>Country Code</th>";
    echo "<th>Valid</th>";
    echo "<th>Number Type</th>";
    echo "<th>From Cache</th>";
    echo "<th>Error</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($result['results'] as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['country_code']) . "</td>";
        echo "<td>" . ($row['valid'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . htmlspecialchars($row['number_type']) . "</td>";
        echo "<td>" . ($row['from_cache'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . (isset($row['error']) ? htmlspecialchars($row['error']) : '-') . "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
}



/**
 * @param mixed $filePath
 * @return void
 */
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        echo $filePath;

        die("Environment file (.env) not found.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue; 
        }

        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}
?>