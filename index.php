
<?php
// Include your functions or place them within this file
require_once('backup_and_encrypt.php'); // Adjust path and filename
require_once('restore_and_decrypt.php'); // Adjust path and filename

// Set default action (backup or restore)
$action = isset($_GET['action']) ? $_GET['action'] : 'backup';

// Handle form submissions if any
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['backup_submit'])) {
        $ftp_server = $_POST['ftp_server'];
        $ftp_username = $_POST['ftp_username'];
        $ftp_password = $_POST['ftp_password'];
        $ftp_port = $_POST['ftp_port'];
        $local_dir = $_POST['local_dir'];
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_password = $_POST['db_password'];
        $password = $_POST['password'];
        $custom_name = $_POST['custom_name'];
        backup_and_encrypt($ftp_server, $ftp_username, $ftp_password, $ftp_port, $local_dir, $db_host, $db_name, $db_user, $db_password, $password, $custom_name);
    } elseif (isset($_POST['restore_submit'])) {
        $password = $_POST['restore_password'];
        $custom_name = $_POST['custom_name'];
        if (isset($_FILES['restore_file'])) {
            $uploaded_file = $_FILES['restore_file'];
            restore_and_decrypt($uploaded_file['tmp_name'], $password, $custom_name);
        } else {
            echo "Please select a file for restore.";
        }
    }
}

// Prepare form data and variables for display
$ftp_server = isset($_POST['ftp_server']) ? $_POST['ftp_server'] : '';
$ftp_username = isset($_POST['ftp_username']) ? $_POST['ftp_username'] : '';
$ftp_password = ''; // Don't pre-fill password field
$ftp_port = isset($_POST['ftp_port']) ? $_POST['ftp_port'] : 21;
$local_dir = isset($_POST['local_dir']) ? $_POST['local_dir'] : './backup';
$db_host = isset($_POST['db_host']) ? $_POST['db_host'] : 'localhost';
$db_name = isset($_POST['db_name']) ? $_POST['db_name'] : '';
$db_user = isset($_POST['db_user']) ? $_POST['db_user'] : '';
$db_password = ''; // Don't pre-fill password field
$password = ''; // Don't pre-fill password field
$custom_name = isset($_POST['custom_name']) ? $_POST['custom_name'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup and Restore Tool</title>
</head>
<body>
    <h1>Backup and Restore Tool</h1>

    <?php if ($action === 'backup'): ?>

    <h2>Backup</h2>
    <form id="backup-form" method="post">
        <label for="ftp_server">FTP Server:</label>
        <input type="text" id="ftp_server" name="ftp_server" value="<?php echo $ftp_server; ?>" required>
        <br>
        <label for="ftp_username">FTP Username:</label>
        <input type="text" id="ftp_username" name="ftp_username" value="<?php echo $ftp_username; ?>" required>
        <br>
        <label for="ftp_password">FTP Password:</label>
        <input type="password" id="ftp_password" name="ftp_password" required>
        <br>
        <label for="ftp_port">FTP Port:</label>
        <input type="number" id="ftp_port" name="ftp_port" value="<?php echo $ftp_port; ?>">
        <br>
        <label for="local_dir">Local Directory:</label>
        <input type="text" id="local_dir" name="local_dir" value="<?php echo $local_dir; ?>" required>
        <br>
        <label for="db_host">Database Host:</label>
        <input type="text" id="db_host" name="db_host" value="<?php echo $db_host; ?>" required>
        <br>
        <label for="db_name">Database Name:</label>
        <input type="text" id="db_name" name="db_name" value="<?php echo $db_name; ?>" required>
        <br>
        <label for="db_user">Database Username:</label>
        <input type="text" id="db_user" name="db_user" value="<?php echo $db_user; ?>" required>
        <br>
        <label for="db_password">Database Password:</label>
        <input type="password" id="db_password" name="db_password" required>
        <br>
        <label for="password">Encryption Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="custom_name">Custom File Name:</label>
        <input type="text" id="custom_name" name="custom_name" value="<?php echo $custom_name; ?>" required>
        <br>
        <button type="submit" name="backup_submit">Start Backup</button>
    </form>

       <?php if ($action === 'restore'): ?>
    <h2>Restore</h2>
    <form id="restore-form" method="post" enctype="multipart/form-data">
        <label for="restore_file">Encrypted File:</label>
        <input type="file" id="restore_file" name="restore_file" required>
        <br>
        <label for="restore_password">Encryption Password:</label>
        <input type="password" id="restore_password" name="restore_password" required>
        <br>
        <label for="custom_name">Custom File Name:</label>
        <input type="text" id="custom_name" name="custom_name" value="<?php echo $custom_name; ?>" required>
        <br>
        <button type="submit" name="restore_submit">Start Restore</button>
    </form>
    <?php endif; ?>

    </body>
</html>

