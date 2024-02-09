<?php
function backup_and_encrypt($ftp_server, $ftp_username, $ftp_password, $ftp_port, $local_dir, $db_host, $db_name, $db_user, $db_password, $password, $custom_name) {
    // Connect to FTP server
    $conn_id = ftp_connect($ftp_server, $ftp_port);
    if (!$conn_id) {
        die('Could not connect to FTP server.');
    }

    // Login with credentials
    $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
    if (!$login_result) {
        die('Login failed.');
    }

    // Download website files and directories recursively
    if (!ftp_mirror($conn_id, ".", $local_dir)) {
        die('Download failed.');
    }

    // Close FTP connection
    ftp_close($conn_id);

    // Connect to database
    $db_conn = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($db_conn->connect_error) {
        die('Database connection failed: ' . $db_conn->connect_error);
    }

    // Dump database schema and data
    $backup_filename = date('Y-m-d_') . $custom_name . '.sql';
    $db_dump = shell_exec("mysqldump -h $db_host -u $db_user -p$db_password $db_name > $local_dir/$backup_filename");
    if (!$db_dump) {
        die('Database dump failed.');
    }

    // Close database connection
    $db_conn->close();

    // Create temporary archive file
    $temp_zip = tempnam(sys_get_temp_dir(), 'backup_');

    // Compress all files in local directory (including database dump)
    $zip = new ZipArchive();
    if (!$zip->open($temp_zip, ZIPARCHIVE::CREATE)) {
        die('Could not create temporary archive.');
    }
    $files = scandir($local_dir);
    foreach ($files as $file) {
        if (!in_array($file, ['.', '..'])) {
            $zip->addFile($local_dir . '/' . $file, $file);
        }
    }
    $zip->close();

    // Encrypt the temporary archive
    $encrypted_file = $temp_zip . '.enc';
    $encrypted_data = encrypt_file($temp_zip, $password);
    file_put_contents($encrypted_file, $encrypted_data);

    // Clean up temporary files
    unlink($temp_zip);

    echo "Backup and encryption complete: " . $encrypted_file . PHP_EOL;

    // NOTE: Remember to remove or securely store the encrypted file after use.
}

function encrypt_file($file_path, $password) {
    // Use a secure encryption library like Mcrypt or Sodium for real deployments
    // This is a simplified example for demonstration purposes
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $key = md5($password);
    $encrypted_data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, file_get_contents($file_path), MCRYPT_MODE_CBC, $iv);
    return $iv . $encrypted_data;
}

// Adjust parameters and call the function

// ... (replace placeholders with your actual credentials and configuration)
