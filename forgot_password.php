<?php

// INCLUDING CONNECTION TO DATABASE
include 'config/connection.php';

// SESSION
session_start();
if (isset($_SESSION['user_id'])) {
    header('location:user_dashboard.php');
}

// FORGOT PASSWORD QUERIES
$error_message = '';
$success_message = '';
if (isset($_POST['submit'])) {

    $email = $_POST['email'];

    if (empty($email)) {
        $error_message = "Please enter your email";
    } else {
        $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_email->execute([$email]);

        if ($check_email->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));

            $update_token = $conn->prepare("UPDATE users SET token = ? WHERE email = ?");
            $update_token->execute([$token, $email]);

            if ($update_token) {
                $reset_link = "localhost/banksystem/reset_password.php?token=" . $token;
                $email_content = "Click the link below to reset your password:\n\n" . $reset_link;

                $success_message = "localhost/banksystem/reset_password.php?token=" . $token;
            } else {
                $error_message = "Failed to initiate password reset.";
            }
        } else {
            $error_message = "Email not found.";
        }
    }
}

?>


<!-- Add the following HTML code to the forgot_password.php file -->
<!DOCTYPE html>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="css/user_style.css">
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
<html>
<head>
    <title>Forgot Password</title>
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
              <div class="text-center mb-5">
                <h1 class="customHeading h3 text-uppercase">Forgot Password</h1>
              </div>
              <form method="POST">
                <div class="custom-form-group">
                  <label class="text-uppercase" for="email">Email</label>
                  <input type="email" id="email" name="email" class="pb-1" /><span class="pb-1"><i class="fas fa-envelope"></i></span>
                </div>
                <div class="mt-5">
                    <button type="submit" name="submit" class="w-100 p-2 d-block custom-btn" >Reset Password</button>
                    <a href="user_login.php" class="w-100 p-2 d-block custom-btn-2 text-center text-decoration-none mt-1">Go back to login</a>
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
<?php if ($success_message): ?>
    <script>
        swal("Reset password token", "<?= $success_message ?>", "success")
            .then(function() {
                window.location.href = 'forgot_password.php';
            });
    </script>
<?php endif ?>

<?php if ($warning_message): ?>
    <script>
        swal("Password reset warning!", "<?=$warning_message?>", "warning");
    </script>
<?php endif?>

<?php if ($error_message): ?>
    <script>
        swal("Password reset error!", "<?=$error_message?>", "error");
    </script>
<?php endif?>
</body>
</html>