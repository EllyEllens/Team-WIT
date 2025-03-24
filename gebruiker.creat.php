<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Nieuwe persoon toevoegen (CREATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voornaam = trim($_POST['voornaam']);
    $achternaam = trim($_POST['achternaam']);
    $geboortedatum = $_POST['geboortedatum'];
    $telefoon = trim($_POST['telefoon']);
    $email = trim($_POST['email']);
    $adres = trim($_POST['adres']);
    $status = $_POST['status']; 
    $geslacht = trim($_POST['geslacht']);
    $rol_id = $_POST['rol_id'];
    $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT); // Beveilig wachtwoord

    // Voeg de persoon toe
    $sql = "INSERT INTO Personen (rol_id, voornaam, achternaam, geboortedatum, telefoon, email, adres, geslacht, status, wachtwoord) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssss", $rol_id, $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $adres, $geslacht, $status, $wachtwoord);

    if ($stmt->execute()) {
        $person_id = $conn->insert_id;

        // Koppel de rol aan de persoon
        $sql_rol_persoon = "INSERT INTO Rol_Persoon (rol_id, person_id) VALUES (?, ?)";
        $stmt_rol = $conn->prepare($sql_rol_persoon);
        $stmt_rol->bind_param("ii", $rol_id, $person_id);
        $stmt_rol->execute();

        echo "<p style='color: green;'>Gebruiker succesvol toegevoegd!</p>";
    } else {
        echo "<p style='color: red;'>Fout bij toevoegen gebruiker: " . $stmt->error . "</p>";
    }
}

// Haal alle rollen op
$sql_rollen = "SELECT rol_id, rol FROM Rollen";
$rollen_result = $conn->query($sql_rollen);

// Haal alle personen op
$sql = "SELECT p.person_id, p.voornaam, p.achternaam, p.geboortedatum, p.telefoon, p.email, p.adres, p.geslacht, p.status, r.rol 
        FROM Personen p 
        LEFT JOIN Rollen r ON p.rol_id = r.rol_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gebruiker Toevoegen</title>
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>
    <h2>Nieuwe Gebruiker Toevoegen</h2>
    <form method="post" action="">
        <input type="text" name="voornaam" placeholder="Voornaam" required><br>
        <input type="text" name="achternaam" placeholder="Achternaam" required><br>
        <input type="date" name="geboortedatum" required><br>
        <input type="text" name="telefoon" placeholder="Telefoon" required><br>
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="wachtwoord" placeholder="Wachtwoord" required><br>
        <select name="geslacht" required>
            <option value="Man">Man</option>
            <option value="Vrouw">Vrouw</option>
            <option value="Anders">Anders</option>
        </select><br>
        <input type="text" name="adres" placeholder="Adres" required><br>
        <select name="status" required>
            <option value="Actief">Actief</option>
            <option value="Deactief">Deactief</option>
        </select><br>
        <select name="rol_id" required>
            <option value="">-- Selecteer een rol --</option>
            <?php 
            if ($rollen_result->num_rows > 0) {
                while($rol = $rollen_result->fetch_assoc()) { 
                    echo '<option value="' . $rol['rol_id'] . '">' . $rol['rol'] . '</option>';
                }
            } else {
                echo '<option value="">Geen rollen beschikbaar</option>';
            }
            ?>
        </select><br>

        <button type="submit">Gebruiker Toevoegen</button>
    </form>
    
    <h2>Gebruikers Lijst</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>Geboortedatum</th>
                <th>Telefoon</th>
                <th>Email</th>
                <th>Geslacht</th>
                <th>Adres</th>
                <th>Status</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['person_id'] . "</td>";
                    echo "<td>" . $row['voornaam'] . "</td>";
                    echo "<td>" . $row['achternaam'] . "</td>";
                    echo "<td>" . $row['geboortedatum'] . "</td>";
                    echo "<td>" . $row['telefoon'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['geslacht'] . "</td>";
                    echo "<td>" . $row['adres'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['rol'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>Geen gebruikers gevonden</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <a href="gebruiker.php">Terug naar overzicht</a>
</body>
</html>

<?php
$conn->close();
?>
