<?php
session_start(); // Start de sessie

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wit"; // Zorg dat de database echt bestaat

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Voeg dit toe voor goede encoding

// Controleren of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $wachtwoord = trim($_POST['wachtwoord']);

    // SQL query om te zoeken naar de gebruiker in zowel de studenten als andere tabellen
    $sql = "SELECT s.student_id, s.email, s.wachtwoord, 'student' AS rol, NULL AS rol_id
            FROM studenten s
            WHERE s.email LIKE ?
            UNION
            SELECT p.person_id, p.email, p.wachtwoord, NULL AS rol, rp.rol_id
            FROM personen p
            JOIN rol_persoon rp ON p.person_id = rp.person_id
            WHERE p.email LIKE ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Controleer of er resultaten zijn
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Controleer het wachtwoord
        if (password_verify($wachtwoord, $user['wachtwoord'])) {
            // Sla de gegevens van de gebruiker op in de sessie
            $_SESSION['user_id'] = $user['student_id'] ?? $user['person_id']; // Gebruik student_id of person_id afhankelijk van het type gebruiker
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['rol_id'] = $user['rol_id'];

            // Doorsturen op basis van het type gebruiker
            if ($user['rol'] == 'student') {
                header("Location: student.start.html");  // Redirect voor studenten
                exit();
            } elseif ($user['rol_id'] == 3) {
                header("Location: docent.dashboard.html"); // Redirect voor docenten (rol_id 3)
                exit();
            } elseif ($user['rol_id'] == 4) {
                header("Location: admin.php"); // Redirect voor admins (rol_id 4)
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            echo "<script>alert('Ongeldig wachtwoord. Probeer opnieuw.');</script>";
        }
    } else {
        echo "<script>alert('Geen account gevonden met dit e-mailadres. Controleer je gegevens.');</script>";
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="FOTO/png" href="assests/img/logo.webp">

   <!--=============== REMIXICONS ===============-->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/js/all.min.js">
   <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

   <!--=============== CSS ===============-->
   <link rel="stylesheet" href="assests/css/style.css">
   
   <title>Inloggen - NATIN MBO</title>
</head>
<body>
   <!--=============== LOGIN IMAGE ===============-->
   <svg class="login__blob" viewBox="0 0 566 840" xmlns="http://www.w3.org/2000/svg">
      <mask id="mask0" mask-type="alpha">
         <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
      </mask>
   
      <g mask="url(#mask0)">
         <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
   
         <!-- Afbeelding -->
         <image class="login__img" href="assests/img/natin.png"/>
      </g>
   </svg>      

   <!--=============== LOGIN ===============-->
   <div class="login container grid" id="loginAccessRegister">
      <div class="login__access">
         <h1 class="login__title">
            <img src="assests/img/logo.webp" alt="Logo" class="login__logo">
            Log in op jouw account.
         </h1>
         
         <div class="login__area">
            <form action="index.php" method="POST" class="login__form">
               <div class="login__content grid">
                  <div class="login__box">
                     <input type="email" name="email" id="email" required placeholder="" class="login__input">
                     <label for="email" class="login__label">Email</label>
                        <i class="ri-mail-fill login__icon"></i>
                  </div>
        
                  <div class="login__box">
                     <input type="password" name="wachtwoord" id="password" required placeholder="" class="login__input">
                     <label for="password" class="login__label">Wachtwoord</label>
                     <i class="ri-eye-off-fill login__icon login__password" id="loginPassword"></i>
                  </div>
               </div>
               <button type="submit" class="login__button">Login</button>
            </form>
         </div>
      </div>
   </div>
   
   <!--=============== MAIN JS ===============-->
   <script src="assests/js/main.js"></script>
</body>
</html>
