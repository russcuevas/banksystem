<?php
include 'config/connection.php';

// SESSION IF NOT LOGIN YOU CAN'T GO TO DIRECT PAGE
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:user_login.php');
}

// UPDATE PROFILE QUERIES
$error_message = '';
$success_message = '';
if (isset($_POST['submit'])) {
    $old_password = sha1($_POST['old_password']);
    $new_password = sha1($_POST['password']);
    $confirm_password = sha1($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match!";
    } elseif (strlen($_POST['password']) < 12) {
        $error_message = "Password must be at least 12 characters long!";
    } else {
        $select_users = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $select_users->execute([$user_id]);

        if ($select_users->rowCount() > 0) {
            $users = $select_users->fetch(PDO::FETCH_ASSOC);

            if ($old_password !== $users['password']) {
                $error_message = "Incorrect old password";
            } else {
                $update_users = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                $update_users->execute([$new_password, $user_id]);

                if ($update_users) {
                    $success_message = "Password successfully updated";
                } else {
                    $error_message = "Failed to update, please try again";
                }
            }
        } else {
            $error_message = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/user_style.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">
            <a style="border: none; padding: 0px; background-color: transparent;" href="user_dashboard.php"><img src="images/atm.png" alt="Sidebar Icon"></a>
            <h3><a style="border: none; font-size: 15px; padding: 0px; background-color: transparent" href="user_dashboard.php">ATM System</a></h3>
          </div>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="user_wallet.php">Wallet</a>
        <a href="user_withdraw.php">Withdraw</a>
        <a href="user_deposit.php">Deposit</a>
        <a href="user_transaction.php">Transactions</a>
    </div>

    <!-- Main content -->
    <div class="main">
        <div class="header">
            <h4 class="mt-2">My Profile</h4>
            <div class="profile">
                <i class="fa fa-user"></i>
                <div class="dropdown">
                <a class="btn btn-info text-white mb-1" href="user_profile.php">Profile</a>
                <a class="btn btn-danger mt-1" href="#" onclick="confirmLogout()">Logout</a>
                </div>
              </div>              
            </div>
 
        <div class="container col-md-6 p-5">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header p-4">
                            <h2 class="mt-3">Update Profile</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="old_password">Old Password</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary mt-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="js/user_js.js"></script>
<?php if ($success_message): ?>
    <script>
        swal("Successfully updated!", "<?= $success_message ?>", "success")
            .then(function() {
                window.location.href = 'user_profile.php';
            });
    </script>
<?php endif ?>

<?php if ($error_message): ?>
    <script>
        swal("Update password error!", "<?php echo $error_message; ?>", "error");
    </script>
<?php endif ?>
<script>
    // SWEETALERT FUNCTIONALITY
    function confirmLogout() {
    setTimeout(() => {
        window.location.href = "components/user_logout.php";
    }, 2000);

    swal({
        title: "Thank you",
        text: "You have been logged out.",
        icon: "success",
        timer: 2000,
        buttons: false,
    });
    }
</script>
</body>
</html>
