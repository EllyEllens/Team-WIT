<?php
// Verbind met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "natin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Nieuwe gebruiker toevoegen (CREATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $geboortedatum = $_POST['geboortedatum'];
    $telefoon = $_POST['telefoon'];
    $email = $_POST['email'];
    $geslacht = $_POST['geslacht'];
    $adres = $_POST['adres'];
    $rol_id = $_POST['rol_id'];

    $sql = "INSERT INTO personen (voornaam, achternaam, geboortedatum, telefoon, email, geslacht, adres, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $rol_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Gebruiker succesvol toegevoegd!</p>";
    } else {
        echo "<p style='color: red;'>Fout bij toevoegen gebruiker: " . $stmt->error . "</p>";
    }
}

// Studenten en hun rollen ophalen uit de database (READ)
$sql = "SELECT personen.id, personen.voornaam, personen.achternaam, personen.geboortedatum, personen.telefoon, personen.email, personen.geslacht, personen.adres, rollen.rol 
        FROM personen 
        LEFT JOIN rollen ON personen.rol_id = rollen.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Toevoegen</title>
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
        <select name="geslacht" required>
            <option value="Man">Man</option>
            <option value="Vrouw">Vrouw</option>
            <option value="Anders">Anders</option>
        </select><br>
        <input type="text" name="adres" placeholder="Adres" required><br>
        <input type="number" name="rol_id" placeholder="Rol ID" required><br>
        <button type="submit">Gebruiker Toevoegen</button>
    </form>
    
    <h2>Personen Lijst</h2>
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
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['voornaam'] . "</td>";
                    echo "<td>" . $row['achternaam'] . "</td>";
                    echo "<td>" . $row['geboortedatum'] . "</td>";
                    echo "<td>" . $row['telefoon'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['geslacht'] . "</td>";
                    echo "<td>" . $row['adres'] . "</td>";
                    echo "<td>" . ($row['rol'] ?? 'Onbekend') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Geen personen gevonden</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <a href="student.php">Terug naar overzicht</a>
</body>
</html>

<?php
$conn->close();
?>
