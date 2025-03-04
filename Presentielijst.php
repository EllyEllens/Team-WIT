<?php
// DATABASE VERBINDING
$host = "localhost"; 
$user = "root"; 
$password = ""; 
$dbname = "presentie_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// PRESENTIE OPSLAAN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_presentie'])) {
        // Opslaan van nieuwe presentie
        $student_id = $_POST['student_id'];
        $les_id = $_POST['les_id'];
        $status = $_POST['status'];
        $opmerking = $_POST['opmerking'];

        // Opslaan van de gegevens in de presentie tabel
        $stmt = $conn->prepare("INSERT INTO presentie (student_id, les_id, status, opmerking) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $les_id, $status, $opmerking);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['submit_update'])) {
        // Update van de status of opmerking
        $presentie_id = $_POST['presentie_id'];
        $status = $_POST['status'];
        $opmerking = $_POST['opmerking'];

        $stmt = $conn->prepare("UPDATE presentie SET status = ?, opmerking = ? WHERE presentie_id = ?");
        $stmt->bind_param("ssi", $status, $opmerking, $presentie_id);
        $stmt->execute();
        $stmt->close();
    }
}

// STUDENTEN & LESSEN OPHALEN
$studenten = $conn->query("SELECT * FROM studenten");
$lessen = $conn->query("SELECT * FROM lessen");
$presenties = $conn->query("SELECT p.*, s.naam, s.voornaam, l.vak, l.datum FROM presentie p
                            JOIN studenten s ON p.student_id = s.student_id
                            JOIN lessen l ON p.les_id = l.les_id
                            ORDER BY l.datum DESC");

// Zoeken
$zoekterm = "";
if (isset($_POST['zoekterm'])) {
    $zoekterm = $_POST['zoekterm'];
    $presenties = $conn->query("SELECT p.*, s.naam, s.voornaam, l.vak, l.datum FROM presentie p
                                JOIN studenten s ON p.student_id = s.student_id
                                JOIN lessen l ON p.les_id = l.les_id
                                WHERE s.naam LIKE '%$zoekterm%' OR l.vak LIKE '%$zoekterm%'
                                ORDER BY l.datum DESC");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentie Registratie</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f9f9f9; 
            margin: 0; 
            padding: 0; 
        }

        h2 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 20px;
        }

        .container { 
            display: flex; 
            flex-direction: column; 
            gap: 20px; 
            padding: 20px; 
            margin: 20px auto;
            max-width: 1000px;
            background-color: #fff;
            border-radius: 10px; 
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); 
            backdrop-filter: blur(8px); /* Blur effect for the background */
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0px 6px 16px rgba(0, 128, 0, 0.15); /* Hover effect */
        }

        form, table { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        form:hover, table:hover {
            box-shadow: 0px 0px 15px rgba(0, 255, 0, 0.1); /* Hover effect */
        }

        input, select, button { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-size: 16px; 
            background: #f4f4f4;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 255, 0, 0.7); /* Green focus effect */
        }

        button { 
            background: #28a745; 
            color: white; 
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        button:hover { 
            background: #218838; 
        }

        .search-bar { 
            margin-bottom: 20px;
            display: flex; 
            justify-content: center;
        }

        .search-bar input {
            width: 300px;
            margin-right: 10px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }

        th { 
            background-color: #28a745; 
            color: white; 
            font-weight: bold;
        }

        td { 
            background-color: #f4f4f4; 
        }

        td:hover {
            background-color: #e1f1e1; /* Hover effect for table rows */
        }

        .status-A { 
            background: #f8d7da; 
            color: #721c24;
        }

        .status-L { 
            background: #fff3cd; 
            color: #856404;
        }

        .status-Z { 
            background: #fff3e3; 
            color: #7c3f00;
        }

        .status-V { 
            background: #d4edda; 
            color: #155724;
        }

        .form-container { 
            display: flex;
            justify-content: space-between;
        }

        .form-container select, .form-container input { 
            width: 48%; 
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Presentie Registratie</h2>
    
    <form method="post" class="search-bar">
        <input type="text" name="zoekterm" placeholder="Zoek op student of vak..." value="<?= $zoekterm ?>">
        <button type="submit">Zoeken</button>
    </form>

    <form method="post">
        <div class="form-container">
            <div>
                <label for="student_id">Student:</label>
                <select name="student_id" required>
                    <option value="">Selecteer een student</option>
                    <?php while ($row = $studenten->fetch_assoc()) { ?>
                        <option value="<?= $row['student_id'] ?>"><?= $row['naam'] . " " . $row['voornaam'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label for="les_id">Les:</label>
                <select name="les_id" required>
                    <option value="">Selecteer een les</option>
                    <?php while ($row = $lessen->fetch_assoc()) { ?>
                        <option value="<?= $row['les_id'] ?>"><?= $row['vak'] . " - " . $row['datum'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="A">Afwezig</option>
            <option value="L">Laat</option>
            <option value="Z">Ziek</option>
            <option value="LB">Laat met brief</option>
            <option value="V">Vrijstelling</option>
        </select>

        <label for="opmerking">Opmerking:</label>
        <input type="text" name="opmerking" placeholder="Optioneel">

        <button type="submit" name="submit_presentie">Opslaan</button>
    </form>

    <h2>Presentie Overzicht</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Les</th>
            <th>Datum</th>
            <th>Status</th>
            <th>Opmerking</th>
            <th>Actie</th>
        </tr>
        <?php while ($row = $presenties->fetch_assoc()) { ?>
            <tr class="status-<?= $row['status'] ?>">
                <td><?= $row['naam'] . " " . $row['voornaam'] ?></td>
                <td><?= $row['vak'] ?></td>
                <td><?= $row['datum'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['opmerking'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="presentie_id" value="<?= $row['presentie_id'] ?>">
                        <select name="status" required>
                            <option value="A" <?= $row['status'] == 'A' ? 'selected' : '' ?>>Afwezig</option>
                            <option value="L" <?= $row['status'] == 'L' ? 'selected' : '' ?>>Laat</option>
                            <option value="Z" <?= $row['status'] == 'Z' ? 'selected' : '' ?>>Ziek</option>
                            <option value="LB" <?= $row['status'] == 'LB' ? 'selected' : '' ?>>Laat met brief</option>
                            <option value="V" <?= $row['status'] == 'V' ? 'selected' : '' ?>>Vrijstelling</option>
                        </select>
                        <input type="text" name="opmerking" value="<?= $row['opmerking'] ?>" required>
                        <button type="submit" name="submit_update">Bijwerken</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
