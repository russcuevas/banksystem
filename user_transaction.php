<?php 
include 'config/connection.php';

// SESSION IF NOT LOGIN YOU CANT GO TO DIRECT PAGE
session_start();
$user_id = $_SESSION['user_id'];
if(!isset($user_id)){
    header('location:user_login.php');
}
?>

<?php 
$query = "SELECT * FROM transactions";
$fetch_transactions = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Transacations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/user_style.css">
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-title">
        <a style="border: none; padding: 0px; background-color: transparent;" href="user_dashboard.php"><img src="images/atm.png" alt="Sidebar Icon"></a>
        <h3><a style="border: none; font-size: 15px; padding: 0px; background-color: transparent;" href="user_dashboard.php">ATM System</a></h3>
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
        <h4 class="mt-2">My Transactions</h4>
        <div class="profile">
            <i class="fa fa-user"></i>
            <div class="dropdown">
            <a class="btn btn-info text-white" href="user_profile.php">Profile</a>
            <a class="btn btn-danger mt-1" href="#" onclick="confirmLogout()">Logout</a>
        </div>
    </div>              
</div>

<!-- PAGINATION -->
<?php
    $limit = 10;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;


    $offset = ($page - 1) * $limit;

    
    $fetch_transactions = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? LIMIT $limit OFFSET $offset");
    $fetch_transactions->execute([$user_id]);

    
    $total_transactions = $conn->prepare("SELECT COUNT(*) as count FROM transactions WHERE user_id = ?");
    $total_transactions->execute([$user_id]);
    $total_count = $total_transactions->fetch(PDO::FETCH_ASSOC)['count'];

    
    $total_pages = ceil($total_count / $limit);
?>
<!-- TABLE -->
<div class="container col-md-11">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header" style="font-weight: 500;">
                <h4 style="margin-top: 10px">All Transactions</h4>
                </div>
                <div class="card-body">
                <div class="row mb-1">
                <div class="col-md-6 offset-md-6 text-end">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                        <button class="btn btn-primary" id="searchBtn">Search</button>
                    </div>
                </div>
                </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($fetch_transactions->rowCount() > 0 ) {
                                while ($transaction = $fetch_transactions->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td><span style='color: red; font-weight: 900;'>" . $transaction['transaction_id'] . "</span></td>";
                                    echo "<td>" . $transaction['name'] . "</td>";
                                    echo "<td>₱" . $transaction['balance'] . "</td>";
                                    echo "<td>₱" . $transaction['deposit'] . "</td>";
                                    echo "<td>₱" . $transaction['withdraw'] . "</td>";
                                    echo "<td>" . $transaction['method'] . "</td>";
                                    echo "<td>" . $transaction['date_transaction'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo '<tr><td colspan="8"><h1 class="text-center" style="padding: 135px;">No Transactions Found</h1></td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Pagination -->
                            <nav aria-label="Pagination">
                                <ul class="pagination">
                                    <?php if ($page > 1) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="user_transaction.php?page=<?php echo ($page - 1); ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                        </li>
                                    <?php } else { ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&laquo;</span>
                                            <span class="sr-only">Previous</span>
                                        </li>
                                    <?php } ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="user_transaction.php?page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                                <?php if ($i == $page) { ?>
                                                    <span class="sr-only">(current)</span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($page < $total_pages) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="user_transaction.php?page=<?php echo ($page + 1); ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </li>
                                    <?php } else { ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&raquo;</span>
                                            <span class="sr-only">Next</span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>
                    <div class="col-md-6">
                <p id="clock" style="font-size: 15px; margin-top: 13px; text-align: end; font-weight: 900;"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="js/user_js.js"></script>
<script>
    // SEARCH FUNCTIONALITY
    const searchInput = document.getElementById("searchInput");
    const table = document.querySelector("table");
    const tableRows = table.getElementsByTagName("tr");

    searchInput.addEventListener("input", function() {
    const searchValue = searchInput.value.toLowerCase();

    for (let i = 1; i < tableRows.length; i++) { // Start from index 1 to skip table headers
        const rowData = tableRows[i].textContent.toLowerCase();
        if (rowData.includes(searchValue)) {
            tableRows[i].style.display = "";
            } else {
            tableRows[i].style.display = "none";
            }
        }
    });
    
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
