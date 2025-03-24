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

// Nieuwe gebruiker toevoegen (CREATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voornaam = trim($_POST['voornaam']);
    $achternaam = trim($_POST['achternaam']);
    $geboortedatum = $_POST['geboortedatum'];
    $telefoon = trim($_POST['telefoon']);
    $email = trim($_POST['email']);
    $adres = trim($_POST['adres']);
    $status = $_POST['status']; 
    $klas_id = $_POST['klas_id']; 
    $wachtwoord = $_POST['wachtwoord'];

    // Beveiliging: controleer of geslacht een geldige waarde is
    $geslacht_options = ['Man', 'Vrouw', 'Anders'];
    $geslacht = in_array($_POST['geslacht'], $geslacht_options) ? $_POST['geslacht'] : 'Anders';

    // Wachtwoord hashen
    $hashed_wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);

    // Voeg de student toe
    $sql = "INSERT INTO studenten (voornaam, achternaam, geboortedatum, telefoon, email, geslacht, adres, status, wachtwoord) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $voornaam, $achternaam, $geboortedatum, $telefoon, $email, $geslacht, $adres, $status, $hashed_wachtwoord);

    if ($stmt->execute()) {
        $student_id = $conn->insert_id;

        // Voeg de student aan de geselecteerde klas toe
        $sql_klas = "INSERT INTO student_klas (student_id, klas_id, status) VALUES (?, ?, ?)";
        $stmt_klas = $conn->prepare($sql_klas);
        $stmt_klas->bind_param("iis", $student_id, $klas_id, $status);
        $stmt_klas->execute();

        echo "<p style='color: green;'>Student succesvol toegevoegd!</p>";
    } else {
        echo "<p style='color: red;'>Fout bij toevoegen student: " . $stmt->error . "</p>";
    }
}

// Haal alle klassen op
$sql_klassen = "SELECT klas_id, naam FROM Klas";
$klassen_result = $conn->query($sql_klassen);

// Studenten ophalen
$sql = "SELECT student_id, voornaam, achternaam, geboortedatum, telefoon, email, geslacht, adres, status FROM studenten";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Student Toevoegen</title>
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
    <link rel="stylesheet" href="assests/css/student.css">
</head>
<body>
    <h2>Nieuwe Student Toevoegen</h2>
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
        <select name="status" required>
            <option value="actief">Actief</option>
            <option value="deactief">Deactief</option>
        </select><br>
        <select name="klas_id" required>
        <option value="">-- Selecteer een klas --</option>
            <?php while($klas = $klassen_result->fetch_assoc()) { ?>
                <option value="<?php echo $klas['klas_id']; ?>"><?php echo $klas['naam']; ?></option>
            <?php } ?>
        </select><br>
        <input type="password" name="wachtwoord" placeholder="Wachtwoord" required><br>
        <button type="submit">Student Toevoegen</button>
    </form>
    
    <h2>Studenten Lijst</h2>
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
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['voornaam'] . "</td>";
                    echo "<td>" . $row['achternaam'] . "</td>";
                    echo "<td>" . $row['geboortedatum'] . "</td>";
                    echo "<td>" . $row['telefoon'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['geslacht'] . "</td>";
                    echo "<td>" . $row['adres'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
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
