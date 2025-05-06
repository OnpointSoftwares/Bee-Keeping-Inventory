<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['loggedIn'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Beekeeping Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="assets/images/beekeeping.jpg" alt="Beekeeping Management System" class="img-fluid">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <?php if (isset($_GET['action']) && $_GET['action'] === 'register') { ?>
                                        <!-- Register Form -->
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Create an Account</h1>
                                        </div>
                                        <form class="user">
                                            <div id="registerMessage"></div>
                                            <div class="form-group mb-3">
                                                <input type="text" class="form-control form-control-user" id="registerFullName" 
                                                    placeholder="Full Name" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="email" class="form-control form-control-user" id="registerUsername" 
                                                    placeholder="Email Address" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form-control-user" id="registerPassword1" 
                                                    placeholder="Password" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form-control-user" id="registerPassword2" 
                                                    placeholder="Repeat Password" required>
                                            </div>
                                            <button type="button" id="register" class="btn btn-primary btn-user btn-block w-100">
                                                Register Account
                                            </button>
                                        </form>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="login.php?action=resetPassword">Forgot Password?</a>
                                        </div>
                                        <div class="text-center">
                                            <a class="small" href="login.php">Already have an account? Login!</a>
                                        </div>
                                    <?php } else if (isset($_GET['action']) && $_GET['action'] === 'resetPassword') { ?>
                                        <!-- Reset Password Form -->
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                        </div>
                                        <form class="user">
                                            <div id="resetPasswordMessage"></div>
                                            <div class="form-group mb-3">
                                                <input type="email" class="form-control form-control-user" id="resetPasswordUsername" 
                                                    placeholder="Email Address" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form-control-user" id="resetPasswordPassword1" 
                                                    placeholder="New Password" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form-control-user" id="resetPasswordPassword2" 
                                                    placeholder="Confirm New Password" required>
                                            </div>
                                            <button type="button" id="resetPasswordButton" class="btn btn-primary btn-user btn-block w-100">
                                                Reset Password
                                            </button>
                                        </form>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="login.php?action=register">Create an Account!</a>
                                        </div>
                                        <div class="text-center">
                                            <a class="small" href="login.php">Already have an account? Login!</a>
                                        </div>
                                    <?php } else { ?>
                                        <!-- Login Form -->
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                        </div>
                                        <form class="user">
                                            <div id="loginMessage"></div>
                                            <div class="form-group mb-3">
                                                <input type="email" class="form-control form-control-user" id="loginUsername" 
                                                    placeholder="Email Address" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control form-control-user" id="loginPassword" 
                                                    placeholder="Password" required>
                                            </div>
                                            <button type="button" id="login" class="btn btn-primary btn-user btn-block w-100">
                                                Login
                                            </button>
                                        </form>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="login.php?action=resetPassword">Forgot Password?</a>
                                        </div>
                                        <div class="text-center">
                                            <a class="small" href="login.php?action=register">Create an Account!</a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/login.js"></script>
</body>
</html>
