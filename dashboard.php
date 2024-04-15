<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <?php
session_start();
// Hvis brukeren ikke er logget inn, omdiriger dem tilbake til innloggingssiden
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
}

// Projekter kommer fra databasen og knyttet til brukerens id
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "prosjekt";

    //sjeker koblingeg mellom webserner og database
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
    //sjekker id til brukeren som er loget inn
$sth = $pdo->prepare('SELECT * FROM prosjekter WHERE user_id = ?');
$sth->execute([$_SESSION['user_id']]);
$projects = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

</head>

<body>
<h1>Velkommen, <?php echo $_SESSION['user_epost']; ?></h1>

<a href="logout.php">Logg ut</a> <!-- må implementere en log ut knapp -->

<h2>Lag et nytt prosjekt:</h2>
<form action="dashboard.php" method="POST">
    <label for="project_name">Prosjektnavn:</label>
    <input type="text" id="project_name" name="project_name" required><br><br>
    <label for="project_description">Beskrivelse:</label>
    <textarea id="project_description" name="project_description" required></textarea><br><br>
    <input type="submit" value="Opprett prosjekt">
</form>
<h2>Mine prosjekter:</h2>
<ul>
    <?php foreach ($projects as $project) : ?>
        <li>
            <?php echo htmlspecialchars($project['name']); ?>
            <button onclick="window.location.href='set_project.php?id=<?php echo $project['id']; ?>'">Gå til prosjekt</button>
        </li> 
        <br>
    <?php endforeach; ?>
</ul>

<?php
    //sjekr om brukeren er loget inn
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Sjekk om vi har data fra et POST request
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "prosjekt";

    // Rens innkommende data for å unngå whitespaces før og etter tekst
    $project_name = trim($_POST['project_name'] ?? '');
    $project_description = trim($_POST['project_description'] ?? '');

    if (empty($project_name)) {
        echo "Navnet på prosjektet kan ikke være tomt."; // Eller håndter dette på en annen måte, f.eks. sett en feilmelding i en sesjon og vis dette til brukeren
        exit; // Avslutt skriptet for å unngå databaseinnsetting av tomme verdier
    }

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO prosjekter (user_id, name, description) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $project_name, $project_description]);
    
        header("Location: dashboard.php"); // Redirect brukeren tilbake til dashboard etter å ha lagt til prosjekt
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

} else {
    echo "Ingen POST data er sendt."; // Eventuelt kan du legge inn en egen håndtering her for når ingen data er sendt
}
?>
</body>
</html>
