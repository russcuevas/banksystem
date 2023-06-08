<?php 

// INCLUDING CONNECTION TO DATABASE
include '../config/connection.php';

// SESSION
session_start();
if(isset($_SESSION['admin_id'])){
   header('location:admin_dashboard.php');
}

// LOGIN QUERIES
$warning_message= '';
$error_message = '';
if(isset($_POST['submit'])){

   $username = $_POST['username'];
   $password = sha1($_POST['password']);

   if(empty($username) || empty($password)){
    $warning_message = "Please fill up all field first";
   }else{
      $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE username = ? AND password = ?");
      $select_admin->execute([$username, $password]);

      if($select_admin->rowCount() > 0){
         $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
         $_SESSION['admin_id'] = $fetch_admin_id['id'];
      }else{
        $error_message = "Incorrect username or password";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>ATM System Login</title>
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

    .custom-btn:hover {
        background : #fff;
        border:none;
        border:2px solid #3498db;
        color:#3498db;
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

    .custom-btn-3 {
        border-radius:32px;
        background : #E8AA42;
        border:2px solid transparent;
        color:#fff;
        height:48px;
    }

    .custom-btn-3:hover {
        background : #fff;
        border:none;
        border:2px solid #E8AA42;
        color:#E8AA42;
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
              <img style="margin-left: 30px;" src="../images/atm_login.png" class="objectFit"  />
            </div>
            <div class="p-4" id="formPanel">
              <div class="text-center mb-5">
                <h1 class="customHeading h3 text-uppercase">Admin | Login</h1>
              </div>
              <form method="POST">
                <div class="custom-form-group">
                  <label class="text-uppercase" for="username">Username</label>
                  <input type="text" id="username" name="username" class="pb-1" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" /><span class="pb-1"><i class="fas fa-user"></i></span>
                </div>
                <div class="custom-form-group mt-3">
                  <label class="text-uppercase" for="password">Password</label>
                  <input type="password" id="password" name="password" class="pb-1" /><span class="pb-1"><i id="showCursor" class="fas fa-eye-slash" onclick="showPassword(event)"></i></span>
                </div>
                <div class="mt-5">
                    <button type="submit" name="submit" class="w-100 p-2 d-block custom-btn" >Login</button>
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
<!-- SWEETALERT SCRIPT -->
<?php if (isset($_SESSION['admin_id'])): ?>
    <script>
        swal({
            title: "Successfully logged in!",
            text: "You have successfully logged in!",
            icon: "success",
            timer: 1000,
            buttons: false,
        }).then(function() {
            setTimeout(function() {
                window.location.href = 'admin_dashboard.php';
            }, 1000);
        });
    </script>
<?php endif ?>

<?php if ($warning_message): ?>
    <script>
        swal("Login warning!", "<?= $warning_message ?>", "warning");
    </script>
<?php endif ?>

<?php if ($error_message): ?>
    <script>
        swal("Login error!", "<?= $error_message ?>", "error");
    </script>
<?php endif ?>
</body>
</html>