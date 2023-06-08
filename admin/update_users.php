<?php
include '../config/connection.php';

// SESSION IF NOT LOGIN YOU CAN'T GO TO DIRECT PAGE
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$warning_message = '';
$error_message = '';
$success_message = '';

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_sha1 = sha1($_POST['password']);
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($number) || empty($username)) {
        $warning_message = "Fill up all required fields first";
    } elseif (!empty($password) && strlen($password) !== 12) {
        $error_message = "Password must be 12 characters long";
    } elseif ($password !== $confirm_password) {
        $error_message = "Password and confirm password do not match";
    } elseif (empty($password) && empty($confirm_password)) {
        $updateValues = [
            'name' => $name,
            'email' => $email,
            'number' => $number,
            'username' => $username,
            'id' => $id
        ];

        $updateUser = $conn->prepare("UPDATE `users` SET name = :name, email = :email, number = :number, username = :username WHERE id = :id");
        $updateUser->execute($updateValues);

        $success_message = "User updated successfully";
    } else {
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $id]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error_message = "Username or email already exists!";
        } else {
            $updateValues = [
                'name' => $name,
                'email' => $email,
                'number' => $number,
                'username' => $username,
                'id' => $id
            ];

            $passwordUpdate = '';
            if (!empty($password)) {
                $updateValues['password'] = $password_sha1;
                $passwordUpdate = ', password = :password';
            }

            $updateUser = $conn->prepare("UPDATE `users` SET name = :name, email = :email, number = :number, username = :username $passwordUpdate WHERE id = :id");
            $updateUser->execute($updateValues);

            $success_message = "User updated successfully";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
    .container {
        border: 1px solid #ccc;
        max-width: 700px;
        padding: 20px;
        margin: 0 auto;
        margin-top: 50px;
    }

    .add-user {
        margin-bottom: 20px;
    }
</style>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">
            <a style="border: none; padding: 0px; background-color: transparent;" href="admin_dashboard.php"><img src="../images/atm.png" alt="Sidebar Icon"></a>
            <h3><a style="border: none; font-size: 15px; padding: 0px; background-color: transparent;" href="admin_dashboard.php">ATM System</a></h3>
          </div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="view_users.php">Users</a>
        <a href="view_transaction.php">Transactions</a>
        <!-- <a href="#">Reports</a> -->
    </div>

    <!-- Main content -->
    <div class="main">
        <div class="header">
            <h4 class="mt-2">Add Users</h4>
            <div class="profile">
                <i class="fa fa-user"></i>
                <div class="dropdown">
                <a class="btn btn-info text-white mb-1" href="admin_profile.php">Profile</a>
                <a class="btn btn-danger" href="#" onclick="confirmLogout()">Logout</a>
                </div>
              </div>              
            </div>
<!-- Update Users -->
<div class="container">
    <div class="add-user">
        <h1 class="text-center">Update User</h1>
        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_user->execute([$id]);
            $user = $select_user->fetch(PDO::FETCH_ASSOC);
            if ($user) {
        ?>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="name">Full name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" oninput="this.value = this.value.replace(/\s/g, '')">
                    </div>
                    <div class="form-group">
                        <label for="number">Phone number:</label>
                        <input type="number" class="form-control" id="number" name="number" value="<?php echo $user['number']; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)">
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" oninput="this.value = this.value.replace(/\s/g, '')">
                    </div>
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" class="form-control" id="password" name="password" oninput="this.value = this.value.replace(/\s/g, '')" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" oninput="this.value = this.value.replace(/\s/g, '')" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary mt-3">Update User</button>
                </form>
        <?php
            } else {
                echo "<p>User not found.</p>";
            }
        } else {
            echo "<p style='text-align: center; margin-top: 20px; color: red;'>User ID not specified.</p>";
        }
        ?>
    </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="../js/admin_js.js"></script>
<script>
    // SWEETALERT FUNCTIONALITY
    function confirmLogout() {
    setTimeout(() => {
        window.location.href = "../components/admin_logout.php";
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

<?php if ($success_message): ?>
    <script>
        swal("Successfully updated user!", "<?= $success_message ?>", "success")
            .then(function() {
                window.location.href = 'update_users.php';
            });
    </script>
<?php endif ?>

<?php if ($warning_message): ?>
    <script>
        swal("Update user warning!", "<?= $warning_message ?>", "warning");
    </script>
<?php endif ?>

<?php if ($error_message): ?>
    <script>
        swal("Update user error! ", "<?php echo $error_message; ?>", "error");
    </script>
<?php endif ?>
</body>
</html>
