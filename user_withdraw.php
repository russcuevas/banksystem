<?php
include 'config/connection.php';
session_start();
$user_id = $_SESSION['user_id'];
if(!isset($user_id)){
    header('location:user_login.php');
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

$success_message = '';
$error_message = '';

if (isset($_POST['withdraw'])) {
    if ($user_id == '') {
        header('location:user_login.php');
    } else {
        $name = '';
        $withdrawal = $_POST['withdraw'];

        if ($withdrawal <= 0) {
            $error_message = "Invalid withdrawal amount please enter a valid amount";
        } elseif ($withdrawal % 20 !== 0) {
            $error_message = "Invalid deposit amount ATM accept only bills";
        } else {
            $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                $name = $fetch_profile['name'];
            }

            $select_balance = $conn->prepare("SELECT balance FROM wallet WHERE user_id = ?");
            $select_balance->execute([$user_id]);
            $current_balance = 0;
            if ($select_balance->rowCount() > 0) {
                $fetch_balance = $select_balance->fetch(PDO::FETCH_ASSOC);
                $current_balance = $fetch_balance['balance'];
            }

            if ($current_balance <= 0) {
                $error_message = "Insufficient balance for withdrawal";
            } elseif ($current_balance < $withdrawal) {
                $error_message = "Insufficient balance for withdrawal";
            } else {
                $new_balance = $current_balance - $withdrawal;

                if ($select_balance->rowCount() > 0) {
                    $update_withdrawal = $conn->prepare("UPDATE wallet SET withdraw = ?, balance = ?, deposit = 0 WHERE user_id = ?");
                    $update_withdrawal->execute([$withdrawal, $new_balance, $user_id]);
                } else {
                    $insert_withdrawal = $conn->prepare("INSERT INTO wallet (user_id, withdraw, balance, deposit, name) VALUES (?, ?, ?, ?, ?)");
                    $insert_withdrawal->execute([$user_id, $withdrawal, $new_balance, 0, $name]);
                }

                date_default_timezone_set('Asia/Manila');
                $date_transaction = date("Y-m-d / h:i:s A");
                $transaction_method = 'Withdraw';

                $transaction_id = generateTransactionId();

                $insert_transaction = $conn->prepare("INSERT INTO transactions (transaction_id, user_id, name, balance, withdraw, deposit, method, date_transaction) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_transaction->execute([$transaction_id, $user_id, $name, $new_balance, $withdrawal, 0, $transaction_method, $date_transaction]);
                $success_message = "Successfully withdrawn amount from the wallet";
            }
        }
    }
}

function generateTransactionId() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $transaction_id = '';

    for ($i = 0; $i < 10; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $transaction_id .= $characters[$index];
    }

    return $transaction_id;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdraw</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/user_style.css">
    <link rel="stylesheet" href="css/function_style.css">
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
        <h4 class="mt-2">Withdraw</h4>
        <div class="profile">
            <i class="fa fa-user"></i>
            <div class="dropdown">
            <a class="btn btn-info text-white mb-1" href="user_profile.php">Profile</a>
            <a class="btn btn-danger mt-1" href="#" onclick="confirmLogout()">Logout</a>
            </div>
            </div>              
        </div>

<div class="container">
    <div class="withdraw-form">
        <h1 class="text-center">Withdrawal Form</h1>
        <?php 
        $select_transaction = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date_transaction DESC LIMIT 1");
        $select_transaction->execute([$user_id]);
        if ($select_transaction->rowCount() > 0) {
            $transaction = $select_transaction->fetch(PDO::FETCH_ASSOC);
        } else {
            $transaction = array('balance' => 0);
        }
        
        $select_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $select_user->execute([$user_id]);
        if ($select_user->rowCount() > 0) {
            $user = $select_user->fetch(PDO::FETCH_ASSOC);
        } else {
            $user = array('name' => '', 'email' => '', 'number' => '');
        }
        ?>
        
        <div class="user-details">
            <p style="font-size: 30px; font-weight: 900;">Name: <span style="font-weight: 400; "> <?php echo $user['name']; ?></span></p>
            <p style="font-size: 30px; font-weight: 900;">Your Balance: <span style="color: red;">₱<?php echo $transaction['balance']; ?></span></p>
            <p style="font-size: 30px; font-weight: 900;">Email: <span style="font-weight: 400; "><?php echo $user['email']; ?></span></p>
            <p style="font-size: 30px; font-weight: 900;">Phone number: <span style="font-weight: 400;"><?php echo $user['number']; ?></span></p>
        </div>

        
        <form method="POST" action="">
            <div class="form-group">
                <label for="withdraw" style="margin-top: 30px;">Withdraw:</label>
                <input type="number" class="form-control" id="withdraw" name="withdraw" required>
            </div>
            <button type="submit" class="btn btn-primary mt-1">Withdraw</button>
        </form>
    </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="js/user_js.js"></script>
<?php if ($success_message): ?>
    <script>
        swal({
            title: "Successfully withdraw!",
            text: "<?php echo $success_message; ?>",
            icon: "success",
            timer: 2000,
            buttons: false,
        }).then(function() {
            window.location.href = 'user_dashboard.php';
        });
    </script>
<?php endif ?>

<?php if ($error_message): ?>
    <script>
        swal({
            title: "Error withdrawal!",
            text: "<?php echo $error_message; ?>",
            icon: "error",
        });
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

