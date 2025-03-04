<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Voeg student toe
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['firstName'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $birthDate = $_POST['birthDate'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    
    // Controleer of 'geslacht' is verzonden
    $geslacht = !empty($_POST['geslacht']) ? $_POST['geslacht'] : 'Niet opgegeven';
    
    // Hashed wachtwoord
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Profielafbeelding uploaden (optioneel)
    $profileImage = null;
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            $profileImage = $targetFile;
        }
    }

    // Query om student toe te voegen
    $sql = "INSERT INTO docent (voor_naam, naam, geboorte, telefoon, email, profiel, geslacht, wachtwoord) 
            VALUES ('$firstName', '$lastName', '$birthDate', '$phoneNumber', '$email', '$profileImage', '$geslacht', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Docent succesvol toegevoegd!";
    } else {
        echo "Fout bij toevoegen Docent: " . $conn->error;
    }
}



// Studenten ophalen uit de database
$sql = "SELECT id, voor_naam, naam, geboorte, telefoon, email, geslacht FROM Docent";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
    <title>Studenten Overzicht</title>
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="student.php">Studenten</a></li>
            <li><a href="docent.php">Docenten</a></li>
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

        <div class="container-wrapper">
            <div class="table-container">
                <h2>Docenten Lijst</h2>
                <table id="studentTable">
                <thead>
    <tr>
        <th>Docent ID</th>
        <th>Voornaam</th>
        <th>Achternaam</th>
        <th>Geboortedatum</th>
        <th>Telefoonnummer</th>
        <th>Email</th>
        <th>Geslacht</th>
    </tr>
</thead>
<tbody>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['voor_naam'] . "</td>";
            echo "<td>" . $row['naam'] . "</td>";
            echo "<td>" . $row['geboorte'] . "</td>";
            echo "<td>" . $row['telefoon'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['geslacht'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Geen docenten gevonden</td></tr>";
    }
    ?>
</tbody>

                </table>
            </div>

            <div class="form-container">
                <h2>Docent Toevoegen</h2>
                <form method="POST" enctype="multipart/form-data">
                    <label for="firstName">Voornaam:</label>
                    <input type="text" id="firstName" name="firstName" required>

                    <label for="lastName">Achternaam:</label>
                    <input type="text" id="lastName" name="lastName" required>

                    <label for="birthDate">Geboortedatum:</label>
                    <input type="date" id="birthDate" name="birthDate" required>

                    <label for="geslacht">Geslacht:</label>
                    <select id="geslacht" name="geslacht" required>
                        <option value="Man">Man</option>
                        <option value="Vrouw">Vrouw</option>
                        <option value="Anders">Anders</option>
                    </select>


                    <label for="phoneNumber">Telefoonnummer:</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" required>

                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Wachtwoord:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="profileImage">Profiel Foto:</label>
                    <input type="file" id="profileImage" name="profileImage" accept="image/*">

                    <button type="submit" class="btn">Voeg Docent Toe</button>
                </form>
            </div>
        </div>

    </div>

    <script src="assests/js/student.js"></script>

</body>
</html>

<?php
$conn->close();
?>
