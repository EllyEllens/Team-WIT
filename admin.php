<?php
// Databaseverbinding instellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natin";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Query om het aantal studenten op te halen
$sql = "SELECT COUNT(*) as total_students FROM student";
$result = $conn->query($sql);

// Haal het aantal studenten op
$students_count = 0;
if ($result) {
    $row = $result->fetch_assoc(); // Resultaat ophalen
    $students_count = $row['total_students']; // Het aantal studenten toewijzen
}

// Sluit de verbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="assests/css/admin.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="student.php">Studenten</a></li>
                <li><a href="docent.php">Docenten</a></li>
                <li><a href="#">Klassen</a></li>
                <li><a href="#">Presentie</a></li>
                <li><a href="index.php">Uitloggen</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <!-- Header with profile and search bar -->
            <header class="header">
                <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
                <div class="header-right">
                    <input type="text" class="search-bar" placeholder="Zoeken...">
                    <img src="assests/img/logo.webp" alt="Profiel" class="profile-pic">
                </div>
            </header>

            <!-- Dashboard Cards -->
            <div class="cards">
                <div class="card">
                    <h3>Aantal Studenten</h3>
                    <!-- Toon het aantal studenten hier -->
                    <p id="students-count"><?php echo $students_count; ?></p>
                </div>
                <div class="card">
                    <h3>Aantal Docenten</h3>
                    <p id="teachers-count">0</p>
                </div>
                <div class="card">
                    <h3>Aantal Klassen</h3>
                    <p id="classes-count">0</p>
                </div>
                <div class="card">
                    <h3>Totale Aanwezigheid</h3>
                    <p id="attendance-count">0</p>
                </div>
            </div>

            <!-- Overview with table and teachers list side by side -->
            <div class="overview-container">
                <!-- Attendance Table -->
                <div class="overview-table">
                    <table id="attendance-table">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Contact</th>
                                <th>Presentie</th>
                                <th>Datum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic table content -->
                        </tbody>
                    </table>
                </div>

                <!-- Teachers List -->
                <div class="teachers-container">
                    <h3>Alle Docenten</h3>
                    <ul id="teachers-list">
                        <!-- Teachers dynamically listed here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="assests/js/admin.js"></script>
</body>
</html>
