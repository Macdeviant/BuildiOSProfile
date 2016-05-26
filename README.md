Just # BuildiOSProfile

https://davidwalsh.name/basic-php-file-handling-create-open-read-write-append-close-delete


I don't do a great deal of file handling in my PHP code -- most of my customers don't have a need for it or there's no room for file creation in the already tight budget. On the rare occasion that I do need to manipulate files, I keep the following tip sheet.

Create a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file
Open a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //open file for writing ('w','r','a')...
Read a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'r');
$data = fread($handle,filesize($my_file));
Write to a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
$data = 'This is the data';
fwrite($handle, $data);
Append to a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);
$data = 'New data line 1';
fwrite($handle, $data);
$new_data = "\n".'New data line 2';
fwrite($handle, $new_data);
Close a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
//write some data here
fclose($handle);
Delete a File
$my_file = 'file.txt';
unlink($my_file);
