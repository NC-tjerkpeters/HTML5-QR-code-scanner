# HTML5-QR-code-scanner
This is a PHP-based web application that utilizes HTML5 QR code scanning to read QR codes. The application scans QR codes, sends the scanned data to a server-side script (store.php), and displays a success or error message using the SweetAlert2 library.

Here's a breakdown of the code:

The PHP section sets some HTTP headers to prevent caching of the web page to ensure that the QR code scanning process is always up-to-date.
It defines the base URL ($base_url) which is later used in the JavaScript section to construct the URL for the server-side script.

The HTML section contains a div element with the ID qr-reader, which will be used as the container for the QR code scanner.

The JavaScript section starts by importing two external scripts: html5-qrcode.min.js for QR code scanning and SweetAlert2 for displaying alert messages.
The Html5QrcodeScanner class is initialized with the container ID (qr-reader), and some options like frames per second (fps) and the size of the QR code box (qrbox).
The onScanSuccess function is called whenever a QR code is successfully scanned. It performs the following steps:
Checks if the scanned QR code is different from the last scanned code (lastScanned). If not, it ignores the result to prevent duplicates.
Constructs the URL and data to be sent to the server-side script (store.php) using XMLHttpRequest.
Sends a POST request to the server-side script with the scanned QR code data as JSON.
Displays a success or error message using SweetAlert2 based on the response received from the server.
The QR code scanner is rendered using the html5QrcodeScanner.render(onScanSuccess) method.
Finally, the onload event handler is used to modify the appearance of the QR code scanner by removing an unnecessary element and adding a custom label.
Please note that this code makes use of outdated versions of the external scripts (html5-qrcode@2.0.9 and SweetAlert2@11). It is recommended to use the latest versions of these libraries for improved security and compatibility. Additionally, always ensure that the server-side script (store.php) handles the incoming data securely and performs necessary validations before processing it.
