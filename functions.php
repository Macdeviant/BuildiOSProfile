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
 

function unzip($file){

    $zip=zip_open(realpath(".")."/".$file);
    if(!$zip) {return("Unable to proccess file '{$file}'");}


    while($zip_entry=zip_read($zip)) {
       $zdir=dirname(zip_entry_name($zip_entry));
       $zname=zip_entry_name($zip_entry);

       if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Unable to proccess file '{$zname}'";continue;}
       if(!is_dir($zdir)) mkdirr($zdir,0777);

       print "{$zdir} | {$zname} \n";

       $zip_fs=zip_entry_filesize($zip_entry);
       if(empty($zip_fs)) continue;

       $zz=zip_entry_read($zip_entry,$zip_fs);

       $z=fopen($zname,"w");
       fwrite($z,$zz);
       fclose($z);
       zip_entry_close($zip_entry);

    } 
    zip_close($zip);

} 

function mkdirr($pn,$mode=null) {

  if(is_dir($pn)||empty($pn)) return true;
  $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);

  if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}

  $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
  if(mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
  return false;
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
