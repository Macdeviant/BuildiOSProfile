<?php
	//Make sure php.ini has 50MB upload file size
	//"upload_max_filesize = 50M"
	//"post_max_size = 50M"
	
	//Alternatively add this to a .htaccess file in the web root.
	//php_value upload_max_filesize 10M
	//php_value post_max_size 10M
	
require 'payload.php'; //makes the payload sections - Thanks to https://github.com/robperc/Payloads for the basics
require 'functions.php'; //has all the 'other' functions to suport getting the payload

switch($_POST['auth_type']) { //for each case. User/Cert/Certs_in_a_zip
	
	// For User Authentication
case 'EUNP': {
			checkInput(['edu_un', 'edu_pw']); //check there is a username and password attached
				$edu_un = rtrim($_POST["edu_un"]); //check there is a user name (trim blank spaces)
				$edu_pw = $_POST["edu_pw"]; //check there is a password but dont trim
					switch($_POST['eduMail_selection']) { //check there is a valid edumail email
						case 'Show': {
						checkInput(['edumailaddy']);
						$edumailaddy = rtrim($_POST["edumailaddy"]); //check there is an email
					if (filter_var($edumailaddy, FILTER_VALIDATE_EMAIL) === false) { //check that is IS an email address with an '@'
    					echo("That's not even not an email address!"); //if not print error
    					exit; //then exit
					 } else {
					 if (stristr($edumailaddy, '@edumail.vic.gov.au') === false) { //check that the suffix is edumail.vic.gov.au
    					echo 'Email address must be a valid edumail email address ending in "edumail.vic.gov.au!"'; //if not print error
    					exit; //then exit
					}
				}
			}
		}
		//Working Section
	} break;
			
	// Bulk Certificates
case 'BulkCERT': {
	if(!isset($_FILES['uploaded_zipfile']) || $_FILES['uploaded_zipfile']['error'] == UPLOAD_ERR_NO_FILE) { //check a file is selected
    	echo "Error: No file selected."; //if not print error
    	exit; //then exit
			} else {
				if($_FILES['uploaded_zipfile']['size'] > 500000000) { //check the file uploaded is under 50MB
        			echo "Error: Exceeded 50MB file size limit."; //if not print error
        			exit; //then exit
        		}						
    	if((!empty($_FILES["uploaded_zipfile"])) && ($_FILES['uploaded_zipfile']['error'] == 0)) {	
    		$uuid0 = (string) gen_uuid(); //create a UUID random number
    		 define('UPLOADS_DIRECTORY', "./upload/$uuid0/"); //Make a working dir
			  mkdir(UPLOADS_DIRECTORY, 0777);							
				if(!move_uploaded_file($_FILES['uploaded_zipfile']['tmp_name'], UPLOADS_DIRECTORY.$_FILES['uploaded_zipfile']['name'])) { //move uploaded file to working dir
					echo "Error: Could not move file. It's probably an intrnal permission error. Please contact Craig Hair @ hair.craig.j@edumail.vic.gov.au"; //if not print error
					exit; //then exit
				} 
			}
		}
		//Working Section

		Unzip(UPLOADS_DIRECTORY,UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name']);
		
	} break;
	
	// Single Certificate
case 'PCERT': {	
	if(!isset($_FILES['uploaded_pfxfile']) || $_FILES['uploaded_pfxfile']['error'] == UPLOAD_ERR_NO_FILE) { //check a file is selected
    	echo "Error: No file selected."; //if not print error
    	exit; //then exit
			} else {
				if($_FILES['uploaded_pfxfile']['size'] > 10000) { //check the file uploaded is under 10KB
        			echo "Error: Exceeded 10KB file size limit."; //if not print error
        			exit; //then exit
        		}					
    	if((!empty($_FILES["uploaded_pfxfile"])) && ($_FILES['uploaded_pfxfile']['error'] == 0)) {
    		$uuid0 = (string) gen_uuid(); //create a UUID random number
    		 define('UPLOADS_DIRECTORY', "./upload/$uuid0/"); //Make a working dir
    		  mkdir(UPLOADS_DIRECTORY, 0777);				
				if(!move_uploaded_file($_FILES['uploaded_pfxfile']['tmp_name'], UPLOADS_DIRECTORY.$_FILES['uploaded_pfxfile']['name'])) { //move uploaded file to working dir
					echo "Error: Could not move file. It's probably an intrnal permission error. Please contact Craig Hair @ hair.craig.j@edumail.vic.gov.au"; //if not print error
					exit; //then exit
				}  

			} 								

		} 									
		//Working Section

		$pureCert = (string) get_cert_data();
		//get_cert_data();
		echo $pureCert;
	} break;


}


echo "<br>";
echo "<br>";
echo "Finish";
?>

