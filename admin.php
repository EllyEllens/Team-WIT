<?php
// Start de sessie
session_start();

// Databaseverbinding instellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit"; // Zorg ervoor dat de database bestaat

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Query om het aantal studenten op te halen
$sql_students = "SELECT COUNT(*) as total_students FROM studenten";
$result_students = $conn->query($sql_students);

// Haal het aantal studenten op
$students_count = 0;
if ($result_students) {
    $row = $result_students->fetch_assoc();
    $students_count = $row['total_students'];
}

// Query om het aantal klassen op te halen
$sql_classes = "SELECT COUNT(*) as total_classes FROM klas";
$result_classes = $conn->query($sql_classes);

// Haal het aantal klassen op
$classes_count = 0;
if ($result_classes) {
    $row = $result_classes->fetch_assoc();
    $classes_count = $row['total_classes'];
}

// Sluit de verbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
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
                <li><a href="gebruiker.php">Gebruikers</a></li>
                <li><a href="klassen.php">Klassen</a></li>
                <li><a href="presentie.php">Presentie</a></li>
                <li><a href="index.php">Uitloggen</a></li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <!-- Header with profile and search bar -->
            <header class="header">
                <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
                <div class="header-right">
                    <img src="assests/img/logo.webp" alt="Profiel" class="profile-pic">
                </div>
            </header>

            <!-- Dashboard Cards -->
            <div class="cards">
                <div class="card">
                    <h3>Aantal Studenten</h3>
                    <p id="students-count"><?php echo $students_count; ?></p>
                </div>
                <div class="card">
                    <h3>Aantal Docenten</h3>
                    <p id="teachers-count">0</p>
                </div>
                <div class="card">
                    <h3>Aantal Klassen</h3>
                    <!-- Toon het aantal klassen hier -->
                    <p id="classes-count"><?php echo $classes_count > 0 ? $classes_count : 'Geen klassen beschikbaar'; ?></p>
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
                                <th>Klasaam</th>
                                <th>Aantal Aanwezigen</th>
                                <th>Aantal Afwezig</th>
                                <th>Aantal Laat komers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamische inhoud van de tabel -->
                        </tbody>
                    </table>
                </div>

                <!-- Teachers List -->
                <div class="teachers-container">
                    <h3>Alle Docenten</h3>
                    <ul id="teachers-list">
                        <!-- Docenten dynamisch weergegeven -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="assests/js/admin.js"></script>
</body>
</html>
