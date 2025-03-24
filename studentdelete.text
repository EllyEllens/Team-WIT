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

// Verwijder gebruiker (DELETE)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // SQL-query aangepast naar de Studenten tabel
    $sql = "DELETE FROM Studenten WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Student succesvol verwijderd!'); window.location.href='student.php';</script>";
        exit();
    } else {
        echo "<p style='color: red;'>Fout bij verwijderen student: " . $stmt->error . "</p>";
    }
}

// Studenten ophalen uit de database (JOIN met Klas en student_klas)
$sql = "SELECT s.student_id, s.voornaam, s.achternaam, s.email, s.status AS student_status, k.naam AS klasnaam, sk.status AS klas_status
        FROM Studenten s
        LEFT JOIN student_klas sk ON s.student_id = sk.student_id
        LEFT JOIN Klas k ON sk.klas_id = k.klas_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Verwijderen</title>
    <link rel="stylesheet" href="assests/css/student.css">
    <script>
        function confirmDelete(id) {
            if (confirm("Weet je zeker dat je deze student wilt verwijderen?")) {
                document.getElementById("deleteForm-" + id).submit();
            }
        }
    </script>
</head>
<body>
    <h2>Student Verwijderen</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>Email</th>
                <th>Status (Student)</th>
                <th>Klas</th>
                <th>Status (Klas)</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr id='row-" . $row['student_id'] . "'>";
                    echo "<td>" . $row['student_id'] . "</td>";
                    echo "<td>" . $row['voornaam'] . "</td>";
                    echo "<td>" . $row['achternaam'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['student_status'] . "</td>";
                    echo "<td>" . ($row['klasnaam'] ?? 'Geen klas') . "</td>";
                    echo "<td>" . ($row['klas_status'] ?? 'N/A') . "</td>";
                    echo "<td>
                            <form id='deleteForm-" . $row['student_id'] . "' method='post' action=''>
                                <input type='hidden' name='delete_id' value='" . $row['student_id'] . "'>
                                <button type='button' onclick='confirmDelete(" . $row['student_id'] . ")'>Verwijderen</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Geen studenten gevonden</td></tr>";
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
