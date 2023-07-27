<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$base_url = 'URL';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QRCode Web Application</title>
	<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
	<div id="qr-reader" style="width: 100%"></div>
	
	<script>
		var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
		var lastScanned;
		var reScan = true

		const onScanSuccess = async (decodedText, decodedResult) => {
			if (decodedText !== lastScanned) {
			// if (reScan) {
			// 	reScan = false
				lastScanned = decodedText
				await console.log(`Code scanned = ${decodedText}`, decodedResult);

				var baseurl = '<?= $base_url; ?>'
				var data = {
					'qrcode_values': decodedText,
				}

				var xmlhttp = new XMLHttpRequest()
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var result = JSON.parse(xmlhttp.responseText)
						if (result.status == 'error') {
							// window.alert(result.message)
							Swal.fire({
								allowOutsideClick: false,
								allowEscapeKey: false,
								allowEnterKey: false,
								confirmButtonText: 'Probeer opnieuw',
								icon: 'error',
								title: 'Oops...',
								text: result.message,
							}).then((result) => {
								if (result.isConfirmed) {
									console.log("confirmed")
								}
								reScan = true
								lastScanned = ''
							})
						} else {
							// window.alert(result.message)
							Swal.fire({
								allowOutsideClick: false,
								allowEscapeKey: false,
								allowEnterKey: false,
								icon: 'success',
								title: 'Gelukt!',
								text: result.message,
								confirmButtonText: 'Close',
							}).then((result) => {
								if (result.isConfirmed) {
									console.log("confirmed")
								}
								reScan = true
								lastScanned = ''
							})
						}
					}
				}

				xmlhttp.open("POST", `${baseurl}/app/func/store.php`)
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8")
				xmlhttp.send(JSON.stringify(data))
			}
		}

		html5QrcodeScanner.render(onScanSuccess);

		window.onload = async function(e) { 
			await document.getElementById("qr-reader").firstChild.firstChild.firstChild.remove()
			let el = document.createElement("span")
			el.innerHTML = "QR Code Scanner"
			await document.getElementById("qr-reader").firstChild.firstChild.appendChild(el)
		}
	</script>
</body>
</html>