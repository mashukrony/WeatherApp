<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Connect to the database
$conn = @new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filter variables
$filter_area = '';
$filter_temperature = '';
$filter_time = '';

// Check if filters are set
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['filter_area'])) {
        $filter_area = $_GET['filter_area'];
    }
    if (isset($_GET['filter_temperature'])) {
        $filter_temperature = $_GET['filter_temperature'];
    }
    if (isset($_GET['filter_time'])) {
        $filter_time = $_GET['filter_time'];
    }
}

// Build the query with filters
$query = 'SELECT weather_updates.*, categories.name as category_name FROM weather_updates LEFT JOIN categories ON weather_updates.category_id = categories.id WHERE 1=1';

if ($filter_area != '') {
    $query .= ' AND area LIKE "%' . $conn->real_escape_string($filter_area) . '%"';
}
if ($filter_temperature != '') {
    $query .= ' AND temperature = ' . $conn->real_escape_string($filter_temperature);
}
if ($filter_time != '') {
    $query .= ' AND time LIKE "%' . $conn->real_escape_string($filter_time) . '%"';
}

$result = @$conn->query($query);

// Set message based on query parameters
$message = isset($_GET['msg']) ? $_GET['msg'] : '';

// Handle comments submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_POST['update_id'])) {
    $comment = $_POST['comment'];
    $update_id = $_POST['update_id'];

    $stmt = @$conn->prepare('INSERT INTO comments (user_id, update_id, comment) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $user_id, $update_id, $comment);
    @$stmt->execute();
    @$stmt->close();
}

// Fetch comments
$comments_query = 'SELECT comments.*, users.username FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE comments.update_id = ?';
$comments_stmt = @$conn->prepare($comments_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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
    <?php endif; ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">WeatherApp</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($role !== 'guest'): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white mx-1 custom-btn" href="weather_update.php">Create Weather Update</a>
                    </li>
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-secondary text-white mx-1 custom-btn" href="manage_users.php">Manage Users</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white mx-1 custom-btn" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-center mb-4">Weather Updates</h2>
        
        <!-- Filtering Form -->
        <form method="get" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="filter_area">Area</label>
                    <input type="text" class="form-control" id="filter_area" name="filter_area" value="<?= htmlspecialchars($filter_area) ?>" placeholder="Filter by area">
                </div>
                <div class="form-group col-md-4">
                    <label for="filter_temperature">Temperature</label>
                    <input type="number" step="0.1" class="form-control" id="filter_temperature" name="filter_temperature" value="<?= htmlspecialchars($filter_temperature) ?>" placeholder="Filter by temperature">
                </div>
                <div class="form-group col-md-4">
                    <label for="filter_time">Time</label>
                    <input type="datetime-local" class="form-control" id="filter_time" name="filter_time" value="<?= htmlspecialchars($filter_time) ?>" placeholder="Filter by time">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>

        <div class="row">
            <?php while ($row = @$result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" class="card-img-top small-img" alt="Weather photo">
                        <div class="card-body">
                            <h5 class="card-title">Time: <?= htmlspecialchars($row['time']) ?></h5>
                            <p class="card-text">Area: <?= htmlspecialchars($row['area']) ?></p>
                            <p class="card-text">Temperature: <?= htmlspecialchars($row['temperature']) ?>°C</p>
                            <p class="card-text">Description: <?= htmlspecialchars($row['description']) ?></p>
                            <p class="card-text">Category: <?= htmlspecialchars($row['category_name']) ?></p>
                            <?php if ($row['user_id'] == $user_id && $role !== 'guest'): ?>
                                <a href="update_weather.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_weather.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this update?');">Delete</a>
                            <?php endif; ?>

                            <!-- Display comments -->
                            <h6 class="mt-4">Comments:</h6>
                            <?php
                            @$comments_stmt->bind_param('i', $row['id']);
                            @$comments_stmt->execute();
                            $comments_result = @$comments_stmt->get_result();
                            while ($comment_row = @$comments_result->fetch_assoc()): ?>
                                <div class="comment mb-2">
                                    <strong><?= htmlspecialchars($comment_row['username']) ?>:</strong>
                                    <p><?= htmlspecialchars($comment_row['comment']) ?></p>
                                    <small><?= htmlspecialchars($comment_row['created_at']) ?></small>
                                </div>
                            <?php endwhile; ?>

                            <?php if ($role !== 'guest'): ?>
                                <!-- Add comment form -->
                                <form method="post" class="mt-3">
                                    <div class="form-group">
                                        <textarea class="form-control" name="comment" placeholder="Add a comment" required></textarea>
                                    </div>
                                    <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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
