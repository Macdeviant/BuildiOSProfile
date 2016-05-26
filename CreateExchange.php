<?php
 
/*
 * Dave's Automatic Email Configuration Profile Generator
 * https://blog.skipfault.com/2013/04/02/create-an-exchange-configuration-profile-using-php/
 * Creates a mobileconfig style plist to automatically provision an Exchange account to an iOS device.
 *
 * @param string --email	email address of user
 * @param string --password	password of user
 * @param string --organisation	company the user works for
 * @param string --uuid		optional manual override of the Universal Unique Identifier
 * @param string --file		optional manual override of the file to be created
 *
 */
 
$longopts = array(
        "email::",
        "password::",
        "organisation::",
        "uuid::",
	"file::",
);
 
$options = getopt("", $longopts);
 
if (empty($options["uuid"])) { $options["uuid"] = gen_uuid(); }
if (empty($options["organisation"])) { $options["organisation"] = "Provided by Reddog Technology"; }
if (empty($options["email"])) { show_help(); exit(); }
if (empty($options["password"])) { show_help(); exit(); }
if (empty($options["file"])) { $options["file"] = preg_replace('/.au$/', "", $options["email"]) ; $options["file"] = preg_replace('/.com$/', "", $options["file"]); $options["file"] = $options["file"].".mobileconfig"; }
 
$profile = sprintf('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>PayloadContent</key>
	<array>
		<dict>
			<key>EmailAddress</key>
			<string>%s</string>
			<key>Host</key>
			<string>mail.7sq.com.au</string>
			<key>MailNumberOfPastDaysToSync</key>
			<integer>14</integer>
			<key>Password</key>
			<string>%s</string>
			<key>PayloadDescription</key>
			<string>Configures device for use with Microsoft Exchange ActiveSync services.</string>
			<key>PayloadDisplayName</key>
			<string>%s</string>
			<key>PayloadIdentifier</key>
			<string>%s</string>
			<key>PayloadOrganization</key>
			<string>%s</string>
			<key>PayloadType</key>
			<string>com.apple.eas.account</string>
			<key>PayloadUUID</key>
			<string>%s</string>
			<key>PayloadVersion</key>
			<integer>1</integer>
			<key>PreventAppSheet</key>
			<false/>
			<key>PreventMove</key>
			<false/>
			<key>SMIMEEnabled</key>
			<false/>
			<key>SSL</key>
			<true/>
			<key>UserName</key>
			<string>%s</string>
		</dict>
	</array>
	<key>PayloadDescription</key>
	<string>%s</string>
	<key>PayloadDisplayName</key>
	<string>%s</string>
	<key>PayloadIdentifier</key>
	<string>%s</string>
	<key>PayloadOrganization</key>
	<string>Organisation</string>
	<key>PayloadRemovalDisallowed</key>
	<false/>
	<key>PayloadType</key>
	<string>Configuration</string>
	<key>PayloadUUID</key>
	<string>%s</string>
	<key>PayloadVersion</key>
	<integer>1</integer>
</dict>
</plist>', $options["email"], $options["password"], $options["email"], $options["uuid"], $options["organisation"], $options["uuid"], $options["email"], $options["email"], $options["email"], $options["uuid"], $options["uuid"]);
 
echo "Configuration profile written to ".$options["file"]."\n";
echo "Is your next step to \"scp ".$options["file"]." webuser@domain.com:/var/www/html/tmp/\" ?\n";
$fileHandle = fopen($options["file"], 'w') or die("Could not open ".$options["file"]);
fwrite($fileHandle, $profile);
fclose($fileHandle);
 
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
 
function show_help() {
	echo "Help for make_profile.php\n\n";
	echo "Usage :\n";
	echo "  Required :\n";
	echo "  --email=someone@somewhere.com\n";
	echo "  --password=password\n";
	echo "Optional :\n";
	echo "  --organisation=\"Company Name\"\n";
	echo "  --uuid=\"overwrite unique identifier here\"\n";
	echo "  --file=\"file to write\"\n";
	echo "\n\n";
	echo "php make_profile.php --email=someone@somewhere.com --password=supersecret --organisation=\"Reddog Technology\"\n";
}
 
?>
