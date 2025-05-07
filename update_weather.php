<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $time = $_POST['time'];
    $area = $_POST['area'];
    $temperature = $_POST['temperature'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    // Ensure the logged-in user is the owner of the post
    $result = $conn->query("SELECT user_id FROM weather_updates WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row['user_id'] != $user_id) {
        echo "Unauthorized action.";
        exit();
    }

    $stmt = $conn->prepare('UPDATE weather_updates SET time = ?, area = ?, temperature = ?, description = ?, category_id = ? WHERE id = ?');
    $stmt->bind_param('ssdsdi', $time, $area, $temperature, $description, $category_id, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: dashboard.php?msg=Update successful');
    exit();
} else {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM weather_updates WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row['user_id'] != $user_id) {
        echo "Unauthorized action.";
        exit();
    }

    // Fetch categories for the dropdown
    $categories = $conn->query('SELECT id, name FROM categories');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Weather Update</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">WeatherApp</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white mx-1 custom-btn" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white mx-1 custom-btn" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-4">Update Weather Update</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <div class="form-group">
                                <label for="time">Time</label>
                                <input type="datetime-local" class="form-control" name="time" value="<?= htmlspecialchars($row['time']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" class="form-control" name="area" value="<?= htmlspecialchars($row['area']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="temperature">Temperature</label>
                                <input type="number" step="0.1" class="form-control" name="temperature" value="<?= htmlspecialchars($row['temperature']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" required><?= htmlspecialchars($row['description']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" name="category_id" required>
                                    <?php while ($cat_row = $categories->fetch_assoc()): ?>
                                        <option value="<?= $cat_row['id'] ?>" <?= $cat_row['id'] == $row['category_id'] ? 'selected' : '' ?>><?= $cat_row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Weather Updates</p>
    </footer>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
