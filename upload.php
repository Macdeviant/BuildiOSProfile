<?php
require 'payload.php';
require 'functions.php';

switch($_POST['auth_type']) {
			// User Authentication
case 'EUNP': {
			checkInput(['edu_un', 'edu_pw']);
				$edu_un = $_POST["edu_un"];
				$edu_pw = $_POST["edu_pw"];
					switch($_POST['eduMail_selection']) {
						// valid email check
						case 'Show': {
						checkInput(['edumailaddy']);
						$edumailaddy = $_POST["edumailaddy"];
					if (filter_var($edumailaddy, FILTER_VALIDATE_EMAIL) === false) {
    					echo("That's not even not an email address!");
    					exit;
					 } else {
					 if (stristr($edumailaddy, '@edumail.vic.gov.au') === false) {
    					echo 'Email address must be a valid edumail email address ending in "edumail.vic.gov.au!"';
    					exit;
					}
				}
			}
		}
	} break;
			
			// Bulk Certificates
case 'BulkCERT': {
	if(!isset($_FILES['uploaded_zipfile']) || $_FILES['uploaded_zipfile']['error'] == UPLOAD_ERR_NO_FILE) {
    	echo "Error: No file selected.";
    	exit; 
			} else {
				if($_FILES['uploaded_zipfile']['size'] > 500000000) {
        			echo "Error: Exceeded 50MB file size limit.";
        			exit;
        		}						
    	if((!empty($_FILES["uploaded_zipfile"])) && ($_FILES['uploaded_zipfile']['error'] == 0)) {	
    		$uuid0 = (string) gen_uuid();
    		 define('UPLOADS_DIRECTORY', "./upload/$uuid0/");
			  mkdir(UPLOADS_DIRECTORY, 0777);							
				if(!move_uploaded_file($_FILES['uploaded_zipfile']['tmp_name'], UPLOADS_DIRECTORY.$_FILES['uploaded_zipfile']['name'])) {
					echo "Error: Could not move file. It's probably an intrnal permission error. Please contact Craig Hair @ hair.craig.j@edumail.vic.gov.au";
					exit;
				} 
			}
		}
		Unzip(UPLOADS_DIRECTORY,$_FILES['uploaded_zipfile']); 
		
	} break;
	
			// Single Certificate
case 'PCERT': {	
	if(!isset($_FILES['uploaded_pfxfile']) || $_FILES['uploaded_pfxfile']['error'] == UPLOAD_ERR_NO_FILE) {
    	echo "Error: No file selected.";
    	exit; 
			} else {
				if($_FILES['uploaded_pfxfile']['size'] > 10000) {
        			echo "Error: Exceeded 10KB file size limit.";
        			exit;
        		}					
    	if((!empty($_FILES["uploaded_pfxfile"])) && ($_FILES['uploaded_pfxfile']['error'] == 0)) {
    		$uuid0 = (string) gen_uuid();
    		 define('UPLOADS_DIRECTORY', "./upload/$uuid0/");
    		  mkdir(UPLOADS_DIRECTORY, 0777);				
				if(!move_uploaded_file($_FILES['uploaded_pfxfile']['tmp_name'], UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) {
					echo "Error: Could not move file. It's probably an intrnal permission error. Please contact Craig Hair @ hair.craig.j@edumail.vic.gov.au";
					exit;
				}  

			} 								

		} 									
		
		$pureCert = (string) get_cert_data();
		echo $pureCert;
	} break;


}


echo "<br>";
echo $uuid0;
echo "<br>";
echo "Finish";
?>
