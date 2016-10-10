<?php

function checkInput($keys) { //check value exists
	foreach ($keys as $key) {
		if (empty($_POST[$key])) {
			die("Error: " . ucfirst($key) . " must be set for config to be valid!");
		}
	}
}

function gen_uuid () { //create random UUID function
		return rtrim(shell_exec("uuidgen"));
	}
	
function gen_uuid2() { //create random UUID function without uuidgen executable
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function get_cert_dataOLD () { //pull cert data out of pfx file using password given
   if(!file_exists(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) {
			echo "File not found.";
			throw new Exception('Error: File not found.', 123);
									exit;
        }

        $rawpfx = file_get_contents(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name']);
        if ($rawpfx === false) {
        	echo "Error: Could not get file contents";
            throw new Exception('Error: Could not get file contents');
                					exit;
        }
        $pfx_Cert_pw = $_POST["pfx_Cert_pw"];
		$pfx_Cert_pw = (!isSet( $pfx_Cert_pw ) || empty( $pfx_Cert_pw ) )? "eduSTAR.NET" : $pfx_Cert_pw;
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
 
 
function get_cert_data ($file) { //pull cert data out of pfx file using password given
   if(!file_exists(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) {
			echo "File not found.";
			throw new Exception('Error: File not found.', 123);
									exit;
        }

        $rawpfx = file_get_contents(UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name']);
        if ($rawpfx === false) {
        	echo "Could not get file contents";
            throw new Exception('Error: Could not get file contents.');
                					exit;
        }
        $pfx_Cert_pw = $_POST["pfx_Cert_pw"];
		$pfx_Cert_pw = (!isSet( $pfx_Cert_pw ) || empty( $pfx_Cert_pw ) )? "eduSTAR.NET" : $pfx_Cert_pw;
		$crts = array();
		$tmpfile = touch("tmpfile");

	if ($pureCert = openssl_pkcs12_read($rawpfx, $crts, $pfx_Cert_pw)) {
	shell_exec("openssl enc -a -in $file -out tmpfile");
	$pureCert = file_get_contents("tmpfile");
    $pureCert = trim($pureCert);
	return $pureCert;

		} else {
    		echo "Error: Unable to read the cert store.\n";
   			throw new Exception("Error: Unable to read the cert store.");
    								exit;
	}
} 

function openssl_enc($file) {
	$tmpfile = touch("tmpfile");
	shell_exec("openssl enc -a -in $file -out tmpfile");
	$pureCert = file_get_contents("tmpfile");
    $pureCert = trim($pureCert);
	return $pureCert;
}

function unziploop(){
		$x = 1;
		do {
		unzip($_FILES['uploaded_zipfile']['name']);
		$x++;
		} while ($x <= 10);
	}

function unzip($file){ //unzip("test.zip");
    $zip=zip_open(realpath(".")."/".$file);
    if(!$zip) {return("Unable to proccess file '{$file}'");}

    $e='';

    while($zip_entry=zip_read($zip)) {
       $zdir=dirname(zip_entry_name($zip_entry));
       $zname=zip_entry_name($zip_entry);

       if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Error: Unable to proccess file '{$zname}'";continue;}
       if(!is_dir($zdir)) mkdirr($zdir,0777);

       #print "{$zdir} | {$zname} \n";

       $zip_fs=zip_entry_filesize($zip_entry);
       if(empty($zip_fs)) continue;

       $zz=zip_entry_read($zip_entry,$zip_fs);

       $z=fopen($zname,"w");
       fwrite($z,$zz);
       fclose($z);
       zip_entry_close($zip_entry);

    } 
    zip_close($zip);

    return($e);
} 

function mkdirr($pn,$mode=null) { //create directory
  if(is_dir($pn)||empty($pn)) return true;
  $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);

  if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}

  $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
  if(mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
  return false;
}


function prettyXML($xml, $debug=false) { //makes xml pretty and readable
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
