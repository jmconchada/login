<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .social-login {
            text-align: center;
            margin-top: 20px;
        }
        .social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 24px;
            color: white;
            text-decoration: none;
            margin: 0 15px; 
            transition: 0.3s ease;
        }
        .facebook-btn {
            background-color: #1877f2;
        }
        .google-btn {
            background-color: #1877f2;
        }
        .social-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="login.php">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder=" " required>
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>
            <p class="recover">
                <a href="#">Recover Password</a>
            </p>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>

        <div class="social-login">
            <p>Or sign in with:</p>
            <a href="https://www.facebook.com/login.php" class="social-btn facebook-btn">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://accounts.google.com/signin" class="social-btn google-btn">
                <i class="fab fa-google"></i>
            </a>
        </div>

        <p class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>
