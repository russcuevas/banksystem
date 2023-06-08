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
    <title>Manage Users</title>
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
            <h4 class="mt-2">Manage Users</h4>
            <div class="profile">
                <i class="fa fa-user"></i>
                <div class="dropdown">
                <a class="btn btn-info text-white mb-1" href="admin_profile.php">Profile</a>
                <a class="btn btn-danger" href="#" onclick="confirmLogout()">Logout</a>
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
        <?php
        $limit = 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $fetch_users = $conn->prepare("SELECT * FROM users LIMIT $limit OFFSET $offset");
        $fetch_users->execute([]);

        $total_users = $conn->prepare("SELECT COUNT(*) as count FROM users");
        $total_users->execute([]);
        $total_count = $total_users->fetch(PDO::FETCH_ASSOC)['count'];

        $total_pages = ceil($total_count / $limit);
        ?>

        <!-- Table -->
        <div class="container col-md-11">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header" style="font-weight: 500;">
                        <h4 style="margin-top: 10px">Registered Users</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="col-md-6 text-start">
                                    <a href="add_users.php" class="btn btn-primary" id="addUserBtn">Add User</a>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                                        <button class="btn btn-primary" id="searchBtn">Search</button>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($total_count > 0) {
                                        while ($user = $fetch_users->fetch(PDO::FETCH_ASSOC)) {
                                            $id = $user['id'];
                                            $name = $user['name'];
                                            $email = $user['email'];
                                            $number = $user['number'];
                                            $username = $user['username'];
                                            $password = $user['password'];
                                            ?>
                                            <tr>
                                                <td><?php echo $id; ?></td>
                                                <td><?php echo $name; ?></td>
                                                <td><?php echo $email; ?></td>
                                                <td><?php echo $number; ?></td>
                                                <td><?php echo $username; ?></td>
                                                <td>
                                                    <a class="btn btn-primary btn-sm" href="update_users.php?id=<?php echo $id; ?>"><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
                                                    <a class="btn btn-danger btn-sm" href="#" onclick="confirmDelete(<?php echo $id; ?>)"><i class="fa-sharp fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="6"><h1 class="text-center mt-2" style="padding: 130px;">No Users Found</h1></td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="row mt-3">
                                <div class="col-md-4 text-start">
                                    <nav aria-label="Pagination">
                                        <ul class="pagination">
                                            <?php if ($page > 1) { ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="view_users.php?page=<?php echo ($page - 1); ?>" aria-label="Previous">
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
                                                    <a class="page-link" href="view_users.php?page=<?php echo $i; ?>">
                                                        <?php echo $i; ?>
                                                        <?php if ($i == $page) { ?>
                                                            <span class="sr-only">(current)</span>
                                                        <?php } ?>
                                                    </a>
                                                </li>
                                            <?php } ?>

                                            <?php if ($page < $total_pages) { ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="view_users.php?page=<?php echo ($page + 1); ?>" aria-label="Next">
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

    // SWEETALERT FUNCTION
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

function confirmDelete(id) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, this user cannot be recovered!",
        icon: "warning",
        buttons: ["Cancel", "Delete"],
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            swal({
                title: "Deleted user",
                text: "User successfully deleted",
                icon: "success",
                timer: 2000,
                buttons: false,
            });
            setTimeout(function() {
                window.location.href = "delete_users.php?id=" + id;
            }, 2000);
        }
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
