<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $photo = $_FILES['photo']['name'];
    $time = $_POST['time'];
    $area = $_POST['area'];
    $temperature = $_POST['temperature'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photo);

    $conn = new mysqli('localhost', 'root', '', 'weather_app');
    $stmt = $conn->prepare('INSERT INTO weather_updates (user_id, photo, time, area, temperature, description, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssdsi', $user_id, $photo, $time, $area, $temperature, $description, $category_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: dashboard.php?msg=Creation successful');
    exit();
}

// Fetch categories for the dropdown
$conn = new mysqli('localhost', 'root', '', 'weather_app');
$categories = $conn->query('SELECT id, name FROM categories');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Weather Update</title>
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
        <h2 class="text-center mb-4">Create Weather Update</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <input type="file" class="form-control" name="photo" required>
                            </div>
                            <div class="form-group">
                                <label for="time">Time</label>
                                <input type="datetime-local" class="form-control" name="time" required>
                            </div>
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" class="form-control" name="area" placeholder="Enter area" required>
                            </div>
                            <div class="form-group">
                                <label for="temperature">Temperature</label>
                                <input type="number" step="0.1" class="form-control" name="temperature" placeholder="Enter temperature" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" placeholder="Enter description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control" name="category_id" required>
                                    <?php while ($row = $categories->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Post Update</button>
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
