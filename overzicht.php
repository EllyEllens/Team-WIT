<?php
// DATABASE VERBINDING
$host = "localhost";
$user = "root";
$password = "";
$dbname = "wit";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// FUNCTION TO SANITIZE INPUTS
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// FILTEREN OP BASIS VAN GESELECTEERDE WAARDEN
$klas_id = isset($_POST['klas_id']) ? sanitizeInput($_POST['klas_id']) : '';
$student_id = isset($_POST['student_id']) ? sanitizeInput($_POST['student_id']) : '';
$lesblok_id = isset($_POST['lesblok_id']) ? sanitizeInput($_POST['lesblok_id']) : '';
$presentie_code = isset($_POST['presentie_code']) ? sanitizeInput($_POST['presentie_code']) : '';

// QUERY VOOR FILTEREN VAN AANWEZIGHEDEN
$aanwezigheden_query = "SELECT a.*, s.voornaam, s.achternaam, l.dag, l.start, l.eind, k.naam AS klas_naam 
                        FROM Aanwezigheid a 
                        JOIN Studenten s ON a.student_id = s.student_id
                        JOIN Lesblok l ON a.lesblok_id = l.lesblok_id
                        JOIN klas k ON a.klas_id = k.klas_id
                        WHERE 1=1";

if ($klas_id) {
    $aanwezigheden_query .= " AND a.klas_id = ?";
}
if ($student_id) {
    $aanwezigheden_query .= " AND a.student_id = ?";
}
if ($lesblok_id) {
    $aanwezigheden_query .= " AND a.lesblok_id = ?";
}
if ($presentie_code) {
    $aanwezigheden_query .= " AND a.presentie_code = ?";
}

$aanwezigheden_query .= " ORDER BY l.dag, l.start";

$stmt = $conn->prepare($aanwezigheden_query);
$types = '';

if ($klas_id) $types .= 'i';
if ($student_id) $types .= 'i';
if ($lesblok_id) $types .= 'i';
if ($presentie_code) $types .= 's';

$params = [];
if ($klas_id) $params[] = $klas_id;
if ($student_id) $params[] = $student_id;
if ($lesblok_id) $params[] = $lesblok_id;
if ($presentie_code) $params[] = $presentie_code;

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// OPHALEN VAN LESSEN EN KLASSEN
$lesblokken = $conn->query("SELECT * FROM Lesblok");
$klassen = $conn->query("SELECT * FROM klas");
$studenten = $conn->query("SELECT student_id, voornaam, achternaam FROM Studenten");

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assests/img/logo.webp">
    <title>Presentie Overzicht</title>
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
        }

        .search-bar { 
            display: flex; 
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar select, .search-bar input {
            width: 200px;
            margin-right: 10px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }

        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }

        th { 
            background-color: hsl(113, 82%, 35%); 
            color: white; 
            font-weight: bold;
        }

        td { 
            background-color: #f4f4f4; 
        }

        td:hover {
            background-color: #e1f1e1;
        }
        button {
        background-color: hsl(113, 82%, 35%); 
        color: white; 
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #0e6402; 
    }
    button:focus {
        outline: none; 
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }
    .home-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #0e6402;
            color: white;
            padding: 12px 20px;
            text-align: center;
            border-radius: 8px;
            display: inline-block;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

    .home-btn:hover {
        background-color: hsl(113, 82%, 35%);
    }
    </style>
</head>
<body>

<div class="container">
    <h2>Presentie Overzicht</h2>

    <a href="docent.dashboard.html" class="home-btn">Terug naar Home</a>

    <form method="post" class="search-bar">
        <select name="klas_id">
            <option value="">Selecteer een klas</option>
            <?php while ($row = $klassen->fetch_assoc()) { ?>
                <option value="<?= $row['klas_id'] ?>" <?= $klas_id == $row['klas_id'] ? 'selected' : '' ?>><?= $row['naam'] ?></option>
            <?php } ?>
        </select>

        <select name="student_id">
            <option value="">Selecteer een student</option>
            <?php while ($row = $studenten->fetch_assoc()) { ?>
                <option value="<?= $row['student_id'] ?>" <?= $student_id == $row['student_id'] ? 'selected' : '' ?>><?= $row['voornaam'] . " " . $row['achternaam'] ?></option>
            <?php } ?>
        </select>

        <select name="lesblok_id">
            <option value="">Selecteer een lesblok</option>
            <?php while ($row = $lesblokken->fetch_assoc()) { ?>
                <option value="<?= $row['lesblok_id'] ?>" <?= $lesblok_id == $row['lesblok_id'] ? 'selected' : '' ?>>
                    <?= $row['dag'] . " " . $row['start'] . " - " . $row['eind'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="presentie_code">
            <option value="">Selecteer een status</option>
            <option value="Aanwezig" <?= $presentie_code == 'Aanwezig' ? 'selected' : '' ?>>Aanwezig</option>
            <option value="Afwezig" <?= $presentie_code == 'Afwezig' ? 'selected' : '' ?>>Afwezig</option>
            <option value="Te laat" <?= $presentie_code == 'Te laat' ? 'selected' : '' ?>>Te laat</option>
            <option value="Laatbrief" <?= $presentie_code == 'Laatbrief' ? 'selected' : '' ?>>Laatbrief</option>
            <option value="Vrijstelling" <?= $presentie_code == 'Vrijstelling' ? 'selected' : '' ?>>Vrijstelling</option>
        </select>

        <button type="submit">Filteren</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Lesblok</th>
                <th>Klas</th>
                <th>Presentie Status</th>
                <th>Opmerking</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['voornaam'] . " " . $row['achternaam'] ?></td>
                    <td><?= $row['dag'] . " " . $row['start'] . " - " . $row['eind'] ?></td>
                    <td><?= $row['klas_naam'] ?></td>
                    <td><?= $row['presentie_code'] ?></td>
                    <td><?= $row['opmerking'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
