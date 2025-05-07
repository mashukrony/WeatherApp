<?php
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'weather_app');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (empty($username) && empty($password)) {
        // Guest login
        $_SESSION['user_id'] = 'guest';
        $_SESSION['role'] = 'guest';
        $message = "Guest login successful";
        echo "<script>alert('$message'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        $stmt = $conn->prepare('SELECT id, password, role FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash, $role);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $message = "Login successful";
            echo "<script>alert('$message'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $message = "Login error: Invalid username or password";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- Include jQuery for JavaScript alerts -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
    <?php if (!empty($message)): ?>
        <script>
            $(document).ready(function() {
                alert("<?= htmlspecialchars($message) ?>");
            });
        </script>
    <?php elseif (isset($_GET['msg'])): ?>
        <script>
            $(document).ready(function() {
                alert("<?= htmlspecialchars($_GET['msg']) ?>");
            });
        </script>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Enter username">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter password">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <form method="post">
                            <button type="submit" class="btn btn-secondary btn-block mt-2">Login as Guest</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="register.php">Don't have an account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
