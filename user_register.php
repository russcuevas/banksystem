<?php 

// INCLUDING CONNECTION TO DATABASE
include 'config/connection.php';

// SESSION
session_start();
if(isset($_SESSION['user_id'])){
   header('location:user_dashboard.php');
}

// REGISTER QUERIES
$success_message = '';
$warning_message = '';
$error_message = '';
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordsha = sha1($_POST['password']);

    if (empty($name) || empty($email) || empty($number) || empty($username) || empty($password)){
        $warning_message = "Please fill up all field first";
    }
    elseif (strlen($password) !== 12) {
        $error_message = "Password must be 12 characters long";
    }
    else {
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error_message = "Username or email already exists";

        } else {
            $stmt = $conn->prepare("INSERT INTO `users` (name, email, number, username, password) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $number, $username, $passwordsha]);
            $success_message = "Registration Successful";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>ATM System Register</title>
    <style>
    .customHeading {
        color:#3498db;
    }
    .custom-form-group label {
        color:#3498db;
        font-size:13px;
        font-weight:bold;
        letter-spacing:2px;
    }
    .custom-form-group span {
        width: 32px;
        border-bottom: none;
        vertical-align: middle;
        color: #3498db;
        display: inline;
    }
    .custom-form-group input {
        width : calc(100% - 32px);
        border:none;
        border-bottom:1px solid #3498db;
        box-sizing:content-box;
        outline:none;
    }
    .custom-btn {
        border-radius:32px;
        background : #3498db;
        border:2px solid transparent;
        color:#fff;
        height:48px;
    }
    
    .custom-btn-2 {
        border-radius:32px;
        background : #E76161;
        border:2px solid transparent;
        color:#fff;
        height:48px;
    }

    .custom-btn-2:hover {
        background : #fff;
        border:none;
        border:2px solid #E76161;
        color:#E76161;
    }

    .custom-btn:hover {
        background : #fff;
        border:none;
        border:2px solid #3498db;
        color:#3498db;
    }
    #formPanel {
        min-width:280px;
        max-width:320px;
        width:100%;
        margin:0 auto;
    }
    .objectFit {
        object-fit:cover;
        width:100%;
        max-width:320px;
        min-height:60vh;
        margin:0 auto
    }
    #showCursor {
        cursor:pointer;
        
    }
    </style>
</head>
<body>
<div class="min-vh-100 d-flex align-items-center">
  <div class="container">
    <div class="row">
      <div class="col-sm-7 mx-auto">
        <div class="shadow-lg">
          <div class="d-flex align-items-center">
            <div class="d-none d-md-block d-lg-block">
              <img style="margin-left: 30px;" src="images/atm_login.png" class="objectFit"  />
            </div>
            <div class="p-4" id="formPanel">
              <div class="text-center mb-3">
                <!-- <h1 class="customHeading h3 text-uppercase">Registration</h1> -->
              </div>
              <form action="" method="POST">
                <div class="custom-form-group">
                  <label class="text-uppercase" for="name">Full name</label>
                  <input type="text" id="name" name="name" class="pb-1" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" />
                </div>
                <div class="custom-form-group mt-3">
                  <label class="text-uppercase" for="email">Email</label>
                  <input type="email" id="email" name="email" class="pb-1" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                </div>
                <div class="custom-form-group mt-3">
                  <label class="text-uppercase" for="number">Phone number</label>
                  <input type="number" id="number" name="number" class="pb-1" value="<?php echo isset($_POST['number']) ? $_POST['number'] : ''; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)" />
                </div>
                <div class="custom-form-group mt-3">
                  <label class="text-uppercase" for="username">Username</label>
                  <input type="text" id="username" name="username" class="pb-1" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" /><span class="pb-1"><i class="fas fa-user"></i></span>
                </div>
                <div class="custom-form-group mt-3">
                  <label class="text-uppercase" for="password">Password</label>
                  <input type="password" id="password" name="password" class="pb-1" /><span class="pb-1"><i id="showCursor" class="fas fa-eye-slash" onclick="showPassword(event)"></i></span>
                </div>
                <div class="mt-3">
                    <button type="submit" name="submit" class="w-100 p-2 d-block custom-btn">Register</button>
                    <a href="user_login.php" class="w-100 p-2 d-block custom-btn-2 text-center text-decoration-none mt-1">Login here</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>

<script>
function showPassword(e){
  var input = document.getElementById('password')
  if(input.type === 'password'){
    input.type = "text"
    e.target.className = "fas fa-eye"
  }else{
    input.type = "password"
    e.target.className = "fas fa-eye-slash"
  }
}
</script>

<?php if ($success_message): ?>
    <script>
        swal({
            title: "Successfully registered!",
            text: "<?php echo $success_message; ?>",
            icon: "success",
            timer: 2000,
            buttons: false,
        });
        setTimeout(function() {
            window.location.href = "user_login.php";
        }, 2000);
    </script>
<?php endif ?>

<?php if ($warning_message): ?>
    <script>
        swal("Registration warning!", "<?= $warning_message ?>", "warning");
    </script>
<?php endif ?>

<?php if ($error_message): ?>
    <script>
        swal("Registration error!", "<?= $error_message ?>", "error");
    </script>
<?php endif ?>
</body>
</html>