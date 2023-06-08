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
    <title>My Wallet</title>
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
        <h4 class="mt-2">My Wallet</h4>
        <div class="profile">
            <i class="fa fa-user"></i>
            <div class="dropdown">
                <a class="btn btn-info text-white" href="user_profile.php">Profile</a>
                <a class="btn btn-danger mt-1" href="#" onclick="confirmLogout()">Logout</a>
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
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-header" style="font-weight: 500;">
                    <h4 style="margin-top: 10px">My Wallet</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $fetch_wallet = $conn->prepare("SELECT * FROM wallet JOIN users ON wallet.user_id = users.id WHERE wallet.user_id = ?");
                            $fetch_wallet->execute([$user_id]);

                            if ($transaction = $fetch_wallet->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $transaction['user_id'] . "</td>";
                                echo "<td>" . $transaction['name'] . "</td>";
                                echo "<td>" . $transaction['email'] . "</td>";
                                echo "<td>" . $transaction['number'] . "</td>";
                                echo "<td>₱" . $transaction['balance'] . "</td>";

                                echo "<td>";
                                echo "<a href='user_withdraw.php' class='btn btn-primary mx-0'>Withdraw</a>";
                                echo "<a href='user_deposit.php' class='btn btn-warning text-white mx-1'>Deposit</a>";
                                echo "</td>";

                                echo "</tr>";
                            } else {
                                $fetch_users = $conn->prepare("SELECT * FROM users WHERE id = ?");
                                $fetch_users->execute([$user_id]);

                                if ($user = $fetch_users->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . $user['id'] . "</td>";
                                    echo "<td>" . $user['name'] . "</td>";
                                    echo "<td>" . $user['email'] . "</td>";
                                    echo "<td>" . $user['number']. "</td>";
                                    echo "<td>₱0</td>";

                                    echo "<td>";
                                    echo "<a href='user_withdraw.php' class='btn btn-primary mx-0'>Withdraw</a>";
                                    echo "<a href='user_deposit.php' class='btn btn-success text-white mx-1'>Deposit</a>";
                                    echo "</td>";

                                    echo "</tr>";
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='5'>No user and wallet found.</td>";
                                    echo "</tr>";
                                }
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
