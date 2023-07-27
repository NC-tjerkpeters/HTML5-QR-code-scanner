<?php
// Set the timezone to Europe/Amsterdam
date_default_timezone_set('Europe/Amsterdam');

// Include database connection
require_once "../config/connection.php";

// Receive JSON data
$post = file_get_contents("php://input");
$data = json_decode($post, true);

// Initialize response array
$response = array();

if (!empty($data['qrcode_values'])) {
    // Sanitize the input
    $qrcodeValue = filter_var($data['qrcode_values'], FILTER_SANITIZE_STRING);

    if ($qrcodeValue === false) {
        $response['status'] = 'error';
        $response['message'] = 'Ongeldige gegevens ontvangen.';
    } else {
        // Check if the QR code value already exists in the table
        $checkQuery = "SELECT usage_count, lastupdate FROM participant_qrcodes WHERE qrcode_value = ?";
        $checkStmt = $conn->prepare($checkQuery);

        if ($checkStmt) {
            $checkStmt->bind_param("s", $qrcodeValue);
            $checkStmt->execute();
            $checkStmt->bind_result($usageCount, $lastUpdate);
            $checkStmt->fetch();
            $checkStmt->close();

            // Get the current timestamp in Europe/Amsterdam timezone
            $currentTimestamp = time();
            // Convert lastupdate to timestamp in Europe/Amsterdam timezone
            $lastUpdateTimestamp = strtotime($lastUpdate);
            // Calculate the time difference in minutes
            $timeDifferenceMinutes = ($currentTimestamp - $lastUpdateTimestamp) / 60;

            if ($timeDifferenceMinutes > 5) {
                // Update usage_count and lastupdate if more than 5 minutes have passed
                if (empty($usageCount)) {
                    // QR code value does not exist, insert the new row
                    $insertQuery = "INSERT INTO participant_qrcodes (qrcode_value, usage_count, lastupdate) VALUES (?, 1, NOW())";
                    $insertStmt = $conn->prepare($insertQuery);

                    if ($insertStmt) {
                        $insertStmt->bind_param("s", $qrcodeValue);

                        if ($insertStmt->execute()) {
                            $response['status'] = 'success';
                            $response['message'] = 'QR-code gescand.';
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Gegevens in de database konden niet worden bijgewerkt.';
                        }

                        $insertStmt->close();
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Fout in databasequery.';
                    }
                } else {
                    // QR code value already exists, update the usage_count and lastupdate
                    $updateQuery = "UPDATE participant_qrcodes SET usage_count = usage_count + 1, lastupdate = NOW() WHERE qrcode_value = ?";
                    $updateStmt = $conn->prepare($updateQuery);

                    if ($updateStmt) {
                        $updateStmt->bind_param("s", $qrcodeValue);

                        if ($updateStmt->execute()) {
                            $response['status'] = 'success';
                            $response['message'] = 'QR-code gescand.';
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Gegevens in de database konden niet worden bijgewerkt.';
                        }

                        $updateStmt->close();
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Error! Fout in databasequery.';
                    }
                }
            } else {
                // If less than 5 minutes have passed, do not update usage_count, just return success message
                $response['status'] = 'error';
                $response['message'] = 'QR-code is al gescand.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Fout in databasequery.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error! Lege of ongeldige gegevens ontvangen.';
}

// Close the database connection
$conn->close();

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>
