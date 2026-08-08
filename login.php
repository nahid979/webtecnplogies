<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bazar Hisab</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h2>🛒 Bazar Hisab</h2>
            <h3>Login</h3>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger">❌ Invalid email or password!</div>
            <?php endif; ?>
            <?php if(isset($_GET['registered'])): ?>
                <div class="alert alert-success">✅ Account created! Please login.</div>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="remember" id="remember" style="width: auto;">
                    <label for="remember" style="margin: 0;">Remember Me</label>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p>Don't have an account? <a href="index.php?action=register">Register</a></p>
        </div>
    </div>
</body>
</html>