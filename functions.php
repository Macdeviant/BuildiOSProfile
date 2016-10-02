<?php

function checkInput($keys) {
	foreach ($keys as $key) {
		if (empty($_POST[$key])) {
			die("" . ucfirst($key) . " must be set for config to be valid!");
		}
	}
}


	//  PayloadUUID function
function gen_uuid () {
		return rtrim(shell_exec("uuidgen"));
	}
	

function get_cert_data () {
   if(!file_exists(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) {
			echo "File not found.1";
			throw new Exception('File not found.1', 123);
									exit;

        }

	if (!is_file(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) {
			echo "File not found.2";
            throw new Exception('File not found.2', 123);
                					exit;

        }

        $rawpfx = file_get_contents(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name']);
        if ($rawpfx === false) {
        	echo "Could not get file contents";
            throw new Exception('Could not get file contents');
                					exit;
        }
        $pfx_Cert_pw = $_POST["pfx_Cert_pw"];
		$pfx_Cert_pw = (!isSet( $pfx_Cert_pw ) || empty( $pfx_Cert_pw ) )? "password" : $pfx_Cert_pw;
		$crts = array();
		
	if ($parsed = openssl_pkcs12_read($rawpfx, $crts, $pfx_Cert_pw)) {
	$pureCert = $crts['cert'];
	$beginCertText = "-----BEGIN CERTIFICATE-----";
    $endCertText = "-----END CERTIFICATE-----";
	$pureCert = str_replace($beginCertText, "", $pureCert);
	$pureCert = str_replace($endCertText, "", $pureCert);
    $pureCert = trim($pureCert);
	return $pureCert;

	
		} else {
    		echo "Error: Unable to read the cert store.\n";
   			throw new Exception("Error: Unable to read the cert store.");
    								exit;
}

}



function unzip_pfx () {
  if(!file_exists(UPLOADS_DIRECTORY.$_FILES['uploaded_zipfile']['name'])) {
			echo "File not found.1";
			throw new Exception('File not found.1', 123);
									exit;

        }

	if (!is_file(UPLOADS_DIRECTORY.$_FILES['uploaded_zipfile']['name'])) {
			echo "File not found.2";
            throw new Exception('File not found.2', 123);
                					exit;

        }

        
        	
        
}
//OR
//Unzip($dir,$file); 

function Unzip($dir, $file, $destiny="") 
{ 
    $dir .= DIRECTORY_SEPARATOR; 
    $path_file = $dir . $file; 
    $zip = zip_open($path_file); 
    $_tmp = array(); 
    $count=0; 
    if ($zip) 
    { 
        while ($zip_entry = zip_read($zip)) 
        { 
            $_tmp[$count]["filename"] = zip_entry_name($zip_entry); 
            $_tmp[$count]["stored_filename"] = zip_entry_name($zip_entry); 
            $_tmp[$count]["size"] = zip_entry_filesize($zip_entry); 
            $_tmp[$count]["compressed_size"] = zip_entry_compressedsize($zip_entry); 
            $_tmp[$count]["mtime"] = ""; 
            $_tmp[$count]["comment"] = ""; 
            $_tmp[$count]["folder"] = dirname(zip_entry_name($zip_entry)); 
            $_tmp[$count]["index"] = $count; 
            $_tmp[$count]["status"] = "ok"; 
            $_tmp[$count]["method"] = zip_entry_compressionmethod($zip_entry); 
            
            if (zip_entry_open($zip, $zip_entry, "r")) 
            { 
                $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)); 
                if($destiny) 
                { 
                    $path_file = str_replace("/",DIRECTORY_SEPARATOR, $destiny . zip_entry_name($zip_entry)); 
                } 
                else 
                { 
                    $path_file = str_replace("/",DIRECTORY_SEPARATOR, $dir . zip_entry_name($zip_entry)); 
                } 
                $new_dir = dirname($path_file); 
                
                // Create Recursive Directory 
                mkdirr($new_dir); 
                

                $fp = fopen($dir . zip_entry_name($zip_entry), "w"); 
                fwrite($fp, $buf); 
                fclose($fp); 

                zip_entry_close($zip_entry); 
            } 
            echo "\n</pre>"; 
            $count++; 
        } 

        zip_close($zip); 
    } 
} 



function prettyXML($xml, $debug=false) {
  // add marker linefeeds to aid the pretty-tokeniser
  // adds a linefeed between all tag-end boundaries
  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

  // now pretty it up (indent the tags)
  $tok = strtok($xml, "\n");
  $formatted = ''; // holds pretty version as it is built
  $pad = 4; // initial indent
  $matches = array(); // returns from preg_matches()

  /* pre- and post- adjustments to the padding indent are made, so changes can be applied to
   * the current line or subsequent lines, or both
  */
  while($tok !== false) { // scan each line and adjust indent based on opening/closing tags

    // test for the various tag states
    if (preg_match('/.+<\/[^>]*>$/', $tok, $matches)) { // open and closing tags on same line
      if($debug) echo " =$tok= ";
      $indent = 0; // no change
    }
    else if (preg_match('/^<\//', $tok, $matches)) { // closing tag
      if($debug) echo " -$tok- ";
      $pad -= 4; //  outdent now
    }
    else if (preg_match('/^<[^>]*[^\/]>.*$/', $tok, $matches)) { // opening tag
      if($debug) echo " +$tok+ ";
      $indent = 4; // don't pad this one, only subsequent tags
    }
    else {
      if($debug) echo " !$tok! ";
      $indent = 0; // no indentation needed
    }
    
    // pad the line with the required number of leading spaces
    $prettyLine = str_pad($tok, strlen($tok)+$pad, ' ', STR_PAD_LEFT);
    $formatted .= $prettyLine . "\n"; // add to the cumulative result, with linefeed
    $tok = strtok("\n"); // get the next token
    $pad += $indent; // update the pad size for subsequent lines
  }
  return $formatted; // pretty format
}



	
?>
