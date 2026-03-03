<?php
session_start();
include '../db.php';

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    // Check if user exists AND password matches
    // Note: We use BINARY to make the password case-sensitive
    $sql = "SELECT * FROM admin_users WHERE username='$user' AND password='$pass'"; 
    $result = $conn->query($sql);
    
    if($result->num_rows > 0){
        // FIX: Use 'admin' to match what index.php looks for
        $_SESSION['admin'] = true; 
        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect Username or Password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #800000; display: flex; align-items: center; justify-content: center; height: 100vh; }</style>
</head>
<body class="bg-light">
    <div class="card p-4 shadow border-0" style="width: 300px;">
        <h4 class="text-center text-danger mb-3">Admin Login</h4>
        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-danger w-100">Login</button>
            <?php if(isset($error)) echo "<div class='text-danger mt-2 text-center small'>$error</div>"; ?>
        </form>
    </div>
</body>
</html>