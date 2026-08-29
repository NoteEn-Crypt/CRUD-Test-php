<?php
    // Start session
    if (!session_id()) {
        session_start();
    }

    // Include database configuration file
    require_once 'dbConfig.php';


    // Set default redirect url
    $redirectURL = 'index.php';

    if (isset($_POST['userSubmit'])) {
        // Get form fields value
        $id = $_POST['id'];
        $first_name = trim(strip_tags($_POST['first_name']));
        $last_name = trim(strip_tags($_POST['last_name']));
        $email = trim(strip_tags($_POST['email']));
        $country = trim(strip_tags($_POST['country']));
        $status = $_POST['status'];

        // Store the submitted field values in the session
        $sessData['postData'] = $_POST;

        $id_str = '';
        if (empty($id)) {
            $id_str = '?id=' . $id;
        }

        // Fields validation
        $errorMsg = '';
        if (empty($first_name)) {
            $errorMsg .= 'Please enter your first name.<br>';
        }
        if (empty($last_name)) {
            $errorMsg .= 'Please enter your last name.<br>';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg .= 'Please enter a valid email.<br>';
        }
        if (empty($country)) {
            $errorMsg .= 'Please enter your country.<br>';
        }

        // Process the form data
        if (empty($errorMsg)) {
            if (!empty($id)) {
                // Update data in PostgreSQL server
                $sql = "UPDATE members SET first_name = ?, last_name = ?, email = ?, country = ?, status = ? WHERE id = ?";
                $query = $conn->prepare($sql);
                $update = $query->execute(array($first_name, $last_name, $email, $country, $status, $id));

                if ($update) {
                    $sessData['status']['type'] = 'success';
                    $sessData['status']['msg'] = 'Member data has been updated successfully.';

                    // Remove submitted filed values from session
                    unset($sessData['postData']);
                } else {
                    $sessData['status']['type'] = 'error';
                    $sessData['status']['msg'] = 'Something went wrong, please try again!';

                    // Set redirect url
                    $redirectURL = 'addEdit.php' . $id_str;
                }
            } else {
                // Insert data in PostgreSQL server
                $sql = "INSERT INTO members (first_name, last_name, email, country, status, created) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array(
                    &$first_name,
                    &$last_name,
                    &$email,
                    &$country,
                    &$status,
                    date("Y-m-d H:i:s")
                );
                $query = $conn->prepare($sql);
                $insert = $query->execute($params);

                if ($insert) {
                    $sessData['status']['type'] = 'success';
                    $sessData['status']['msg'] = 'Member data has been added successfully.';

                    // Remove submitted field values from session
                    unset($sessData['postData']);
                } else {
                    $sessData['status']['type'] = 'error';
                    $sessData['status']['msg'] = 'Something went wrong, please try again';

                    // Set redirect url
                    $redirectURL = 'addEdit.php' . $id_str;
                }
            }

        } else {
            $sessData['status']['type'] = 'error';
            $sessData['status']['msg'] = 'Please fill all the mandatory fields:<br>' . trim($errorMsg, '<br>');

            // Set redirect url
            $redirectURL = 'addEdit.php' . $id_str;
        }

        // Store status into the session
        $_SESSION['sessData'] = $sessData;
    } else if (($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])) {
        $id = $_GET['id'];

        // Delete data from PostgreSQL server
        $sql = "DELETE FROM members WHERE id = ?";
        $query = $conn->prepare($sql);
        $delete = $query->execute(array($id));

        if ($delete) {
            $sessData['status']['type'] = 'success';
            $sessData['status']['msg'] = 'Member data has been deleted successfully';
        } else {
            $sessData['status']['type'] = 'error';
            $sessData['status']['msg'] = 'Something went wrong, please try again!';
        }

        // Store data into the session
        $_SESSION['sessData'] = $sessData;
    }

    // Redirect to the respective page
    header("Location:" . $redirectURL);
    exit();
?>