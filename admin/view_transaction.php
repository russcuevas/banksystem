<?php 
include '../config/connection.php';

// SESSION IF NOT LOGIN YOU CANT GO TO DIRECT PAGE
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

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
        <h4 class="mt-2">Transactions</h4>
        <div class="profile">
            <i class="fa fa-user"></i>
            <div class="dropdown">
                <a class="btn btn-info text-white mb-1" href="admin_profile.php">Profile</a>
                <a class="btn btn-danger" href="#" onclick="confirmLogout()">Logout</a>
            </div>
        </div>
    </div>

    <?php
    $limit = 10;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $fetch_transactions = $conn->prepare("SELECT * FROM transactions LIMIT $limit OFFSET $offset");
    $fetch_transactions->execute([]);

    $total_transactions = $conn->prepare("SELECT COUNT(*) as count FROM transactions");
    $total_transactions->execute([]);
    $total_count = $total_transactions->fetch(PDO::FETCH_ASSOC)['count'];

    $total_pages = ceil($total_count / $limit);
    ?>

    <!-- Table -->
    <div class="container col-md-11">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header" style="font-weight: 500;">
                <h4 style="margin-top: 10px">Transactions Table</h4>
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
                            <?php if ($fetch_transactions->rowCount() > 0) { ?>
                                <?php while ($transaction = $fetch_transactions->fetch(PDO::FETCH_ASSOC)) { ?>
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
                                    <td colspan="7"><h1 class="text-center mt-2" style="padding: 130px;">No Transactions Found</h1></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="row mt-3">
                        <div class="col-md-4 text-start">
                            <nav aria-label="Pagination">
                                <ul class="pagination">
                                    <?php if ($page > 1) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="view_transaction.php?page=<?php echo ($page - 1); ?>" aria-label="Previous">
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
                                            <a class="page-link" href="view_transaction.php?page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                                <?php if ($i == $page) { ?>
                                                    <span class="sr-only">(current)</span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($page < $total_pages) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="view_transaction.php?page=<?php echo ($page + 1); ?>" aria-label="Next">
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
                        <div class="col-md-4 text-center mt-3">
                            <p id="clock" style="font-size: 15px; font-weight: 900;"></p>
                        </div>

                        <div class="col-md-4 text-end">
                        <?php
                            if ($total_count > 0) {
                                echo '<div class="d-flex justify-content-end">';
                                echo '<a class="btn btn-primary text-white" href="print_transaction.php">Print Transaction</a>';
                                echo '</div>';
                            }
                        ?>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="../js/admin_js.js"></script>
<script>
    // SEARCH FUNCTIONALITY
    const searchInput = document.getElementById("searchInput");
    const table = document.querySelector("table");
    const tableRows = table.getElementsByTagName("tr");

    searchInput.addEventListener("input", function() {
    const searchValue = searchInput.value.toLowerCase();

    for (let i = 1; i < tableRows.length; i++) {
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
