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

    // Include database configuration file
    require_once "dbConfig.php";

    // Fetch the data from PostgreSQL server
    $sql = "SELECT * FROM members ORDER BY id DESC";
    $query = $conn->prepare($sql);
    $query->execute();
    $members = $query->fetchAll(PDO::FETCH_ASSOC);
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

        <div class="row">
            <div>
                <h5>Members</h5>

            <!-- Add link -->
            <div>
                <a href="addEdit.php"><i class="plus"></i> New Member</a>
            </div>
            </div>

            <!-- Display status message -->
            <?php if (!empty($statusMsg)) { ?>
                <div>
                    <div><?php echo $statusMsgType; ?></div>
                </div>
            <?php } ?>

            <!-- List the members -->
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($members)) {
                            $count = 0;
                            foreach ($members as $row) {
                                $count++;
                                ?>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['first_name']; ?></td>
                                <td><?php echo $row['last_name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['country']; ?></td>
                                <td><?php echo $row['status'] == 1 ? '<span>Active</span>' : '<span>Blocked</span>'; ?></td>
                                <td><?php echo $row['created']; ?></td>
                                <td>
                                    <a href="addEdit.php?id=<?php echo $row['id']; ?>">edit</a>
                                    <a href="userAction.php?action_type=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure to delete?');">delete</a>
                                </td>
                                <?php

                            }
                        } else{
                            echo '<tr><td colspan="8">No member(s) found...</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>