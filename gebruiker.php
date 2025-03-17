<?php
// Databaseverbinding instellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit";
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Ophalen van rollen
$sql_roles = "SELECT rol_id, soort FROM Rollen";
$result_roles = $conn->query($sql_roles);
$roles = [];
if ($result_roles->num_rows > 0) {
    while ($row = $result_roles->fetch_assoc()) {
        $roles[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rol_id = $_POST['rol_id'];
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $geboortedatum = $_POST['geboortedatum'];
    $email = $_POST['email'];
    $telefoon = $_POST['telefoon'];
    $adres = $_POST['adres'];
    $geslacht = $_POST['geslacht'];
    $status = $_POST['status'];
    
    // Gebruiker invoegen in Personen-tabel
    $sql = "INSERT INTO Personen (rol_id, voornaam, achternaam, geboortedatum, email, telefoon, adres, geslacht, status) 
            VALUES ('$rol_id', '$voornaam', '$achternaam', '$geboortedatum', '$email', '$telefoon', '$adres', '$geslacht', '$status')";
    
    if ($conn->query($sql) === TRUE) {
        $person_id = $conn->insert_id;
        
        // Koppel de persoon aan de rol in Rol_Persoon
        $sql_role_person = "INSERT INTO Rol_Persoon (rol_id, person_id) VALUES ('$rol_id', '$person_id')";
        $conn->query($sql_role_person);
        
        echo "<p style='color: green;'>Gebruiker succesvol aangemaakt!</p>";
    } else {
        echo "<p style='color: red;'>Fout: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Aanmaken</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Nieuwe Gebruiker Aanmaken</h2>
        <form method="POST" action="">
            <label>Rol:</label>
            <select name="rol_id" required>
                <?php foreach ($roles as $role) { ?>
                    <option value="<?php echo $role['rol_id']; ?>">
                        <?php echo ucfirst($role['soort']); ?>
                    </option>
                <?php } ?>
            </select>
            
            <label>Voornaam:</label>
            <input type="text" name="voornaam" required>
            
            <label>Achternaam:</label>
            <input type="text" name="achternaam" required>
            
            <label>Geboortedatum:</label>
            <input type="date" name="geboortedatum" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Telefoon:</label>
            <input type="text" name="telefoon">
            
            <label>Adres:</label>
            <textarea name="adres"></textarea>
            
            <label>Geslacht:</label>
            <select name="geslacht" required>
                <option value="M">Man</option>
                <option value="V">Vrouw</option>
                <option value="X">Anders</option>
            </select>
            
            <label>Status:</label>
            <select name="status" required>
                <option value="actief">Actief</option>
                <option value="inactief">Inactief</option>
            </select>
            
            <button type="submit">Gebruiker Aanmaken</button>
        </form>
    </div>
</body>
</html>
