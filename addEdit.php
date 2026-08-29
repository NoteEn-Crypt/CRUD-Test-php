<?php
    // Start session
    if (!session_id()) {
        session_start();
    }

    // Retrieve session data
    $sessData = !empty($_SESSION['sessData']) ? $_SESSION['sessData'] : '';

    // Get status message from session
    if (!empty($sessData['status']['msg'])) {
        $statusMsg = $sessData['status']['msg'];
        $statusMsgType = $sessData['status']['type'];
        $statusMsgType = ($statusMsgType == 'error') ? 'danger' : $statusMsgType;
        unset($_SESSION['sessData']['status']);
    }

    // Get member data
    $memberData = $postData = array();
    if (!empty($_GET['id'])) {
        // Include database configuration file
        require_once 'dbConfig.php';

        // Fetch data from PostgresSQL server by row ID
        $sql = "SELECT * FROM members WHERE id = ".$_GET['id'];
        $query = $conn->prepare($sql);
        $query->execute();
        $memberData = $query->fetch(PDO::FETCH_ASSOC);
    }
    $postData = !empty($sessData['postData']) ? $sessData['postData'] : $memberData;
    unset($_SESSION['sessData']['postData']);

    $actionLabel = !empty($_GET['id']) ? 'Edit' : 'Add';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>
</head>
<body>
    <div class="container">
        <h1>CRUD</h1>
        <hr>
        
        <div>
            <div>
                <h5><?php echo $actionLabel; ?> Member</h5>

                <div>
                    <a href="index.php"><-- Back</a>
                </div>
            </div>
        </div>

        <!-- Display status message -->
        <?php if (!empty($statusMsg)) { ?>
            <div>
                <div><?php echo $statusMsgType; ?></div>
            </div>
        <?php } ?>

        <!-- Add/Edit from fields -->
        <div>
            <form action="userAction.php" method="post">
                <div>
                    <label for="">First Name</label>
                    <input type="text" name="first_name" placeholder="Enter your first name" value="<?php echo !empty($postData['first_name']) ? $postData['first_name'] : ''; ?>" required>
                </div>
                <div>
                    <label for="">Last Name</label>
                    <input type="text" name="last_name" placeholder="Enter your last name" value="<?php echo !empty($postData['last_name']) ? $postData['last_name'] : ''; ?>" required>
                </div>
                <div>
                    <label for="">Email</label>
                    <input type="email" name="email" placeholder="Enter your email" value="<?php echo !empty($postData['email']) ? $postData['email'] : ''; ?>" required>
                </div>
                <div>
                    <label for="">Country</label>
                    <input type="text" name="country" placeholder="Enter country name" value="<?php echo !empty($postData['country']) ? $postData['country'] : ''; ?>" required>
                </div>
                <div>
                    <label for="">Status</label>
                    <div>
                        <input type="radio" name="status" value="1" <?php echo !isset($postData['status']) || (!empty($postData['status']) && $postData['status'] == 1) ? 'checked' : ''; ?>>
                        <label for="">Active</label>
                    </div>
                    <div>
                        <input type="radio" name="status" value="0" <?php echo isset($postData['status']) && $postData['status'] == 0 ? 'checked' : ''; ?>>
                        <label for="">Block</label>
                    </div>
                </div>

                <input type="hidden" name="id" value="<?php echo !empty($memberData['id']) ? $memberData['id'] : ''; ?>">
                <input type="submit" name="userSubmit" value="Submit">
            </form>
        </div>
    </div>
</body>
</html>