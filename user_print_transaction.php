<?php 
include 'config/connection.php';

// SESSION IF NOT LOGIN YOU CAN'T GO TO DIRECT PAGE
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
    header('location:user_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Transaction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/user_print_transaction.css">
    <link rel="stylesheet" href="print.css" media="print">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 500px;
            margin-top: 20px;
        }

        .atm-logo {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .atm-logo img {
            margin-left: 10px;
        }

        @media print{
            .print-btn {
            display: none;
        }

        .back-btn{
            display: none;
        }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-info">
            <h1 class="atm-logo">
                <span>ATM System</span>
                <img src="images/atm.png" alt="Company Logo" width="50">
            </h1>
            <p>Mataasnakahoy Batangas</p>
        </div>

        <div class="receipt-box">
            <table class="table">
                <tbody>
                    <?php 
                    $query = "SELECT * FROM transactions ORDER BY id DESC LIMIT 1";
                    $fetch_transactions = $conn->query($query);
                    ?>
                    <?php
                    if ($transaction = $fetch_transactions->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td colspan='7'>";
                        echo "-------------------------------<br>";
                        echo "Receipt for: " . $transaction['name'] . "<br>";
                        echo "Transaction ID: " . $transaction['transaction_id'] . "<br>";
                        echo "Date: " . $transaction['date_transaction'] . "<br>";
                        echo "-------------------------------<br>";
                        echo "Deposit: ₱" . $transaction['deposit'] . "<br>";
                        echo "Withdrawal: ₱" . $transaction['withdraw'] . "<br>";
                        echo "New Balance: ₱" . $transaction['balance'] . "<br>";
                        echo "-------------------------------<br>";
                        echo "<span style=' font-weight: 900; font-size: 14px;'>Thank you for using our ATM. Please come back again!</span><br>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

        <button class="print-btn btn btn-primary text-white" onclick="window.print()">Print Transaction</button><br>
        <a class="back-btn btn btn-danger mt-1" href="user_dashboard.php" style="text-decoration: none;">Go Back</a>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

