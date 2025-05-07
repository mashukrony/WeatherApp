<!DOCTYPE html>
<html>
<head>
    <title>Weather Updates</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-5">
        <h1>Welcome to Weather Updates</h1>
        <p>Your go-to platform for real-time weather updates</p>
    </header>
    
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">WeatherApp</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white mx-1" href="register.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-success text-white mx-1" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <section class="container my-5">
        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                <h2>About Us</h2>
                <p>This platform allows users to post, view, and manage weather updates. Users can register and log in to create and manage their own weather updates, including photos, time, area, temperature, and descriptions.</p>
                <a href="register.php" class="btn btn-primary btn-lg mt-3">Get Started</a>
            </div>
        </div>
    </section>
    
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Weather Updates</p>
    </footer>
    
    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
