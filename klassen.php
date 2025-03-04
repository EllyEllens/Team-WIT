<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasinformatie</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
            margin: 0;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #0e6402; /* Darker professional color */
            color: #fff;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: -250px;
            top: 0;
            bottom: 0;
            transition: left 0.3s ease;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .sidebar ul {
            list-style-type: none;
            padding-left: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 12px;
            display: block;
            border-radius: 8px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #3a4a57;
            padding-left: 20px;
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        /* Header Styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: hsl(113, 82%, 35%);
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-toggle {
            font-size: 30px;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .search-bar {
            padding: 10px;
            font-size: 1rem;
            margin-right: 20px;
            border-radius: 20px;
            border: 1px solid #ccc;
            width: 200px;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        /* Main content styling */
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 80px; /* Zorg ervoor dat de content niet achter de header komt */
            margin-left: 260px; /* Dit verschuift de inhoud naar rechts wanneer de sidebar zichtbaar is */
            transition: margin-left 0.3s ease;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card h3 {
            margin: 10px 0;
            font-size: 1.2em;
        }

        .card p {
            margin: 5px 0;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            background-color: #0e6402;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn:hover {
            background-color: #3a4a57;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .btn:active {
            background-color: #3a4a57;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .add-button-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .add-button-container button {
            padding: 12px 25px;
            background-color: #0e6402;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-button-container button:hover {
            background-color: #3a4a57;
        }

        .add-button-container button:focus {
            outline: none;
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal input,
        .modal button {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .modal button {
            background-color: #0e6402;
            color: white;
            cursor: pointer;
        }

        .modal button:hover {
            background-color: #3a4a57;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="student.php">Gebruikers</a></li>
            <li><a href="klassen.php">Klassen</a></li>
            <li><a href="#">Presentie</a></li>
            <li><a href="index.php">Uitloggen</a></li>
        </ul>
    </div>

    <!-- Header with profile and search bar -->
    <header class="header">
        <button class="sidebar-toggle" id="sidebar-toggle">☰</button>
        <div class="header-right">
            <input type="text" class="search-bar" placeholder="Zoeken...">
            <img src="assests/img/logo.webp" alt="Profiel" class="profile-pic">
        </div>
    </header>

    <h1 style="margin-top: 80px;">Overzicht van Klasgegevens</h1>

    <!-- Knop om een nieuwe klas toe te voegen -->
    <div class="add-button-container">
        <button id="addClassBtn">Voeg een nieuwe klas toe</button>
    </div>

    <!-- Modal voor het toevoegen van een klas -->
    <div id="addClassModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nieuwe Klas Toevoegen</h2>
            <form action="add_klas.php" method="POST">
                <label for="className">Klasnaam:</label>
                <input type="text" id="className" name="className" required>
                <button type="submit">Voeg Klas Toe</button>
            </form>
        </div>
    </div>

    <div class="container">
    <?php
        // Verbind met de database en haal de data op
        $conn = new mysqli("localhost", "root", "", "wit");  // Pas de gegevens aan

        // Controleer de verbinding
        if ($conn->connect_error) {
            die("Verbinding mislukt: " . $conn->connect_error);
        }

        // Pas de query aan om ook de naam van het leerjaar op te halen via een JOIN
        $sql = "SELECT klas.id, klas.naam AS klasnaam, leerjaar.naam AS leerjaar_naam
                FROM klas
                INNER JOIN leerjaar ON klas.leerjaar_id = leerjaar.leerjaar_id";
        $result = $conn->query($sql);

        // Controleer of de query succesvol was
        if (!$result) {
            die("Query mislukt: " . $conn->error);  // Geeft het foutbericht van de query
        }

        // Maak een kaart voor elke klas
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                echo "<h3>" . $row['klasnaam'] . "</h3>";
                echo "<p><strong>Leerjaar:</strong> " . $row['leerjaar_naam'] . "</p>";  // Toon het leerjaar
                echo "<div class='button-container'>";
                echo "<a href='?klas=" . $row['id'] . "' class='btn'>Bekijk Details</a>";
                echo "<a href='delete_klas.php?id=" . $row['id'] . "' class='btn' style='background-color: red;'>Verwijder Klas</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Geen klassen beschikbaar.</p>";
        }

        if (isset($_GET['klas'])) {
            $klas_id = $_GET['klas'];
            $sql = "SELECT naam FROM klas WHERE id = $klas_id";
            $result = $conn->query($sql);

            if (!$result) {
                die("Query mislukt: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "<h2>Presentie van Klas: " . $row['naam'] . "</h2>";
            } else {
                echo "<p>Geen gegevens gevonden voor de geselecteerde klas.</p>";
            }
        }

        $conn->close();
    ?>
</div>


    <script>
        // Sidebar toggle functionaliteit
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            document.querySelector('.container').style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
        });

        // Modal script
        var modal = document.getElementById("addClassModal");
        var btn = document.getElementById("addClassBtn");
        var span = document.getElementsByClassName("close")[0];

        // Toon de modal wanneer de knop wordt geklikt
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Sluit de modal wanneer de 'X' wordt geklikt
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Sluit de modal wanneer er buiten de modal wordt geklikt
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
