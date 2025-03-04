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

// Verwijder gebruiker (DELETE)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $sql = "DELETE FROM personen WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Gebruiker succesvol verwijderd!'); window.location.href='student.php';</script>";
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij verwijderen gebruiker: " . $stmt->error . "</p>";
    }
}

// Studenten en hun rollen ophalen uit de database (READ)
$sql = "SELECT personen.id, personen.voornaam, personen.achternaam, personen.email, rollen.rol 
        FROM personen 
        LEFT JOIN rollen ON personen.rol_id = rollen.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruiker Verwijderen</title>
    <link rel="stylesheet" href="assests/css/student.css">
    <script>
        function confirmDelete(id) {
            if (confirm("Weet je zeker dat je deze gebruiker wilt verwijderen?")) {
                document.getElementById("deleteForm-" + id).submit();
            }
        }
    </script>
</head>
<body>
    <h2>Gebruiker Verwijderen</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr id='row-" . $row['id'] . "'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['voornaam'] . "</td>";
                    echo "<td>" . $row['achternaam'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . ($row['rol'] ?? 'Onbekend') . "</td>";
                    echo "<td>
                            <form id='deleteForm-" . $row['id'] . "' method='post' action=''>
                                <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                                <button type='button' onclick='confirmDelete(" . $row['id'] . ")'>Verwijderen</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Geen gebruikers gevonden</td></tr>";
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