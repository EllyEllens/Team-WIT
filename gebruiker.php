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

// Personen ophalen uit de database (READ)
$sql = "SELECT person_id, voornaam, achternaam, geboortedatum, email, geslacht, adres, status FROM Personen";

$result = $conn->query($sql);

if ($result === false) {
    die("Fout bij het ophalen van personen: " . $conn->error);
}

// Verwijder de gebruiker via een AJAX-aanroep
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['person_id'])) {
    $person_id = $_POST['person_id'];

    // Verwijder de bijbehorende rol_persoon records
    $sql_delete_roles = "DELETE FROM rol_persoon WHERE person_id = ?";
    $stmt_roles = $conn->prepare($sql_delete_roles);
    $stmt_roles->bind_param("i", $person_id);

    if ($stmt_roles->execute()) {
        // Daarna de gebruiker zelf verwijderen
        $sql_delete_person = "DELETE FROM Personen WHERE person_id = ?";
        $stmt_person = $conn->prepare($sql_delete_person);
        $stmt_person->bind_param("i", $person_id);

        if ($stmt_person->execute()) {
            echo "Gebruiker succesvol verwijderd";
        } else {
            echo "Fout bij het verwijderen van de gebruiker: " . $conn->error;
        }

        $stmt_person->close();
    } else {
        echo "Fout bij het verwijderen van gekoppelde rollen: " . $conn->error;
    }

    $stmt_roles->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <title>Gebruikers Overzicht</title>
    <link rel="stylesheet" href="assests/css/student.css">
    <script>
        // Functie om pop-up bevestiging te tonen en de student te verwijderen via AJAX
        function confirmDelete(personId) {
            var confirmation = confirm("Weet je zeker dat je deze persoon wilt verwijderen?");
            if (confirmation) {
                // Verzend de POST-aanroep via AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "", true); // We verzenden naar de huidige pagina
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText); // Toon bericht bij succes
                        location.reload(); // Vernieuw de pagina om de veranderingen weer te geven
                    }
                };
                xhr.send("person_id=" + personId);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Gebruikers Overzicht</h2>
        </div>
        <div class="add-user-container">
            <a href="gebruiker.creat.php" class="add-user-btn">Gebruiker Toevoegen</a>
            <a href="admin.php" class="add-user-btn">Terug naar Home</a>
        </div>
        <div class="main-content">
            <div class="container-wrapper">
                <div class="table-container">
                    <table id="personenTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Voornaam</th>
                                <th>Achternaam</th>
                                <th>Geboortedatum</th>
                                <th>Email</th>
                                <th>Geslacht</th>
                                <th>Adres</th>
                                <th>Status</th>
                                <th>Acties</th>
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
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['geslacht'] . "</td>";
                                    echo "<td>" . $row['adres'] . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "<td>
                                            <a href='gebruiker.update.php?id=" . $row['person_id'] . "' class='edit-btn'>Bewerken</a>
                                            <a href='#' onclick='confirmDelete(" . $row['person_id'] . ")' class='delete-btn'>Verwijderen</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>Geen gebruikers gevonden</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
