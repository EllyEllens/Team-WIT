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

// FILTER OP KLAS
$klas_id = isset($_POST['klas_id']) ? sanitizeInput($_POST['klas_id']) : '';

// STUDENTEN OPHALEN VOOR GESELECTEERDE KLAS
$studenten_result = null;
if ($klas_id != '') {
    $studenten_query = "SELECT s.student_id, s.voornaam, s.achternaam 
                        FROM Studenten s
                        JOIN student_klas sk ON s.student_id = sk.student_id
                        WHERE sk.klas_id = ?";
    
    $stmt = $conn->prepare($studenten_query);
    $stmt->bind_param("i", $klas_id);
    $stmt->execute();
    $studenten_result = $stmt->get_result();
    $stmt->close();
}

// STUDENTEN, LESSEN & KLASSEN OPHALEN
$lesblokken = $conn->query("SELECT * FROM Lesblok");
$klassen = $conn->query("SELECT * FROM klas");

// FILTER OP BASIS VAN KLAS EN ZOEKTERMEN
$aanwezigheden_query = "SELECT a.*, s.voornaam, s.achternaam, l.dag, l.start, l.eind, k.naam AS klas_naam 
                        FROM Aanwezigheid a 
                        JOIN Studenten s ON a.student_id = s.student_id
                        JOIN Lesblok l ON a.lesblok_id = l.lesblok_id
                        JOIN klas k ON a.klas_id = k.klas_id";

if (isset($_POST['zoekterm']) || $klas_id != '') {
    $zoekterm = isset($_POST['zoekterm']) ? sanitizeInput($_POST['zoekterm']) : '';
    $aanwezigheden_query .= " WHERE (s.voornaam LIKE ? OR s.achternaam LIKE ? OR k.naam LIKE ?)";
    
    if ($klas_id != '') {
        $aanwezigheden_query .= " AND k.klas_id = ?";
    }

    $aanwezigheden_query .= " ORDER BY l.dag, l.start";

    $stmt = $conn->prepare($aanwezigheden_query);
    $like_term = "%" . $zoekterm . "%";
    if ($klas_id != '') {
        $stmt->bind_param("ssss", $like_term, $like_term, $like_term, $klas_id);
    } else {
        $stmt->bind_param("sss", $like_term, $like_term, $like_term);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($aanwezigheden_query);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">
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
            backdrop-filter: blur(8px); 
            transition: all 0.3s ease;
        }

        .container:hover {
            box-shadow: 0px 6px 16px rgba(0, 128, 0, 0.15); 
        }

        .search-bar { 
            display: flex; 
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 300px;
            margin-right: 10px;
        }

        form, table { 
            background: #ffffff; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 0 5px rgba(0, 255, 0, 0.7);
        }

        button { 
            background: hsl(113, 82%, 35%); 
            color: white; 
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        button:hover { 
            background: hsl(113, 82%, 35%); 
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
            color: #0e6402;
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

    <a href="student.dashboard.html" class="home-btn">Terug naar Home</a>
    
    <!-- Zoeken en filteren -->
    <form method="post" class="search-bar">
        
        <!-- Klas Filter -->
        <select name="klas_id">
            <option value="">Selecteer Klas</option>
            <?php while ($klas = $klassen->fetch_assoc()) { ?>
                <option value="<?= $klas['klas_id'] ?>" <?= $klas['klas_id'] == $klas_id ? 'selected' : '' ?>><?= $klas['naam'] ?></option>
            <?php } ?>
        </select>
        
        <button type="submit">Zoeken</button>
    </form>

    <h3>Aanwezigheden</h3>
    <table>
        <tr>
            <th>Student</th>
            <th>Lesblok</th>
            <th>Presentie</th>
            <th>Opmerking</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['voornaam'] . " " . $row['achternaam'] ?></td>
                <td><?= $row['dag'] . " " . $row['start'] . " - " . $row['eind'] ?></td>
                <td class="status-<?= $row['presentie_code'] ?>"><?= $row['presentie_code'] ?: 'Niet aanwezig' ?></td>
                <td><?= $row['opmerking'] ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
