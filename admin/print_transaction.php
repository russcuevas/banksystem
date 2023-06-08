<?php 
include '../config/connection.php';

// SESSION IF NOT LOGIN YOU CANT GO TO DIRECT PAGE
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

$query = "SELECT * FROM transactions";
$stmt = $conn->query($query);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/print_transaction_style.css">
    <title>Print Transactions</title>
    <style>
        @media print{
            .print-btn{
                display: none;
            }
            .back-btn{
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-company">
        <h1 style="font-size: 35px;">ATM System</h1>
        <div class="receipt-logo">
            <img src="../images/atm.png" alt="Company Logo">
        </div>
    </div>
    <div class="receipt-address">
        <p>Barangay Mataasnakahoy Batangas</p>
    </div>
    <table class="receipt-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Full Name</th>
                <th>Balance</th>
                <th>Deposit</th>
                <th>Withdraw</th>
                <th>Method</th>
                <th>Date Transact</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transactions) > 0) { ?>
                <?php foreach ($transactions as $transaction) { ?>
                    <tr>
                        <td><span style='color: red; font-weight: 900;'><?php echo $transaction['transaction_id']; ?></span></td>
                        <td><?php echo $transaction['name']; ?></td>
                        <td>₱<?php echo $transaction['balance']; ?></td>
                        <td>₱<?php echo $transaction['deposit']; ?></td>
                        <td>₱<?php echo $transaction['withdraw']; ?></td>
                        <td><?php echo $transaction['method']; ?></td>
                        <td><?php echo $transaction['date_transaction']; ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7"><h1 class="text-center">No Transactions Found</h1></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="view_transaction.php" class="back-btn btn btn-danger mt-1" style="float: right">Go Back</a>
    <button class="print-btn btn btn-primary text-white mt-1" onclick="printTransactions()" style="float: right;">Print Transactions</button>


    <script>
        function printTransactions() {
            window.print();
        }
    </script>
</body>
</html>
