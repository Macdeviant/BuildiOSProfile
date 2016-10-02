<?php
//use php extractpfx.php /path/tocert.pfx "pass phrase"
if(!file_exists($argv[1])) {
			echo "File not found.";
			throw new Exception('File not found.', 123);
									exit;
        }

	if (!is_file($argv[1])) {
			echo "File not found.";
            throw new Exception('File not found.', 123);
                					exit;
        }

        $rawpfx = file_get_contents($argv[1]);
        if ($rawpfx === false) {
        	echo "Could not get file contents";
            throw new Exception('Could not get file contents');
                					exit;
        }
        
        $crts = array();
        
	if ($parsed = openssl_pkcs12_read($rawpfx, $crts, (isset($argv[2])) ? $argv[2] : "")) {
	$pureCert = $crts['cert'];
	$beginCertText = "-----BEGIN CERTIFICATE-----";
    $endCertText = "-----END CERTIFICATE-----";
	$pureCert = str_replace($beginCertText, "", $pureCert);
	$pureCert = str_replace($endCertText, "", $pureCert);
    $pureCert = trim($pureCert);
	//echo $pureCert;
	
		} else {
    		echo "Error: Unable to read the cert store.\n";
   			throw new Exception("Error: Unable to read the cert store.");
    								exit;
}
	
?>
