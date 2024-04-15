<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "prosjekt";
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM prosjekter WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $project = $stmt->fetch();

        if ($project) {
            $_SESSION['prosjektnavn'] = $project['name']; // Eller hva nå enn kolonnenavnet for prosjektets navn er
            $_SESSION['prosjekt_ID'] = $project['id'];
            
            // Redirect til prosjektet etter at session variablene er satt
            header("Location: prosjekt.php");
            exit;
        } else {
            echo "Ingen prosjekt med denne ID-en ble funnet.";
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
} else {
    echo "Ingen prosjekt-ID ble mottatt.";
}
            
// Videresender tilbake til prosjektlisten eller en annen passende side hvis det skjer en feil.
header("Location: prosjektliste.php");
exit();