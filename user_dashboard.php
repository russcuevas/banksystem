<?php 
include 'config/connection.php';

// SESSION IF NOT LOGIN YOU CANT GO TO DIRECT PAGE
session_start();
$user_id = $_SESSION['user_id'];
if(!isset($user_id)){
    header('location:user_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard</title>
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
        <h4 class="mt-2">My Dashboard</h4>
        <div class="profile">
            <i class="fa fa-user"></i>
            <div class="dropdown">
                <a class="btn btn-info text-white" href="user_profile.php">Profile</a>
                <a class="btn btn-danger mt-1" href="#" onclick="confirmLogout()">Logout</a>
            </div>
            </div>              
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            My Profile
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $user_id = $_SESSION['user_id'];

                                $fetch_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                                $fetch_user->execute([$user_id]);
                                $users = $fetch_user->fetch(PDO::FETCH_ASSOC);

                                echo '<h5 class="card-title">Welcome ' . $users['username'] . '</h5>';
                            } else {
                                echo '<h5 class="card-title">0</h5>';
                            }
                            ?>
                            <a href="user_profile.php" class="card-text text-decoration-none">View Profile.</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            My Balance
                        </div>
                        <div class="card-body">
                        <?php
                            $total_balance = 0;
                            $select_balance = $conn->prepare("SELECT balance FROM `wallet` WHERE user_id = ?");
                            $select_balance->execute([$_SESSION['user_id']]);
                            while ($fetch_balance = $select_balance->fetch(PDO::FETCH_ASSOC)) {
                                $total_balance += $fetch_balance['balance'];
                            }
                        ?>
                        <h5 class="card-title"><span>₱</span><?= $total_balance; ?></h5>
                        <a href="user_wallet.php" class="card-text text-decoration-none">View Wallet</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            My Transactions
                        </div>
                        <div class="card-body">
                            <?php
                                $total_transactions = 0;
                                $select_transactions = $conn->prepare("SELECT * FROM `transactions` WHERE user_id = ?");
                                $select_transactions->execute([$_SESSION['user_id']]);
                                $total_transactions = $select_transactions->rowCount();
                            ?>
                            <h5 class="card-title"><?= $total_transactions; ?></h5>
                            <a href="user_transaction.php" class="card-text text-decoration-none">View Transactions.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <!-- <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            Total Reports
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">0</h5>
                            <a href="" class="card-text text-decoration-none">View Users.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Table -->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header" style="font-weight: 500;">
                        <h4 style="margin-top: 10px">My Recent Transaction</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Full Name</th>
                                        <th>Balance</th>
                                        <th>Deposit</th>
                                        <th>Withdraw</th>
                                        <th>Method</th>
                                        <th>Date Transact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                date_default_timezone_set('Asia/Manila');
                                $fetch_transactions = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date_transaction DESC LIMIT 1");
                                $fetch_transactions->execute([$user_id]);

                                if ($transaction = $fetch_transactions->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td><span style='color: red; font-weight: 900;'>" . $transaction['transaction_id'] . "</span></td>";
                                    echo "<td>" . $transaction['name'] . "</td>";
                                    echo "<td>₱" . $transaction['balance'] . "</td>";
                                    echo "<td>₱" . $transaction['deposit'] . "</td>";
                                    echo "<td>₱" . $transaction['withdraw'] . "</td>";
                                    echo "<td>" . $transaction['method'] . "</td>";
                                    echo "<td>" . $transaction['date_transaction'] . "</td>";
                                    echo "<td><a href='user_print_transaction.php' class='btn btn-primary text-white'>Print Transaction</a></td>";
                                    echo "</tr>";
                                } else {
                                    echo '<tr><td colspan="8"><h1 class="text-center" style="padding: 90px;">No Recent Transaction Found</h1></td></tr>';
                                }
                                ?>
                            </tbody>
                            </table>
                            <p id="clock" style="font-size: 15px; text-align:end; font-weight: 900;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="js/user_js.js"></script>
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
    // LIVE CLOCK FUNCTIONALITY
    function updateClock() {
        var date = new Date();
        var options = { timeZone: 'Asia/Manila', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
        var timeStr = date.toLocaleDateString('en-US', options);
        document.getElementById('clock').textContent = timeStr;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
</body>
</html>
