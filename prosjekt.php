<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "prosjekt";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $pdo->prepare("DELETE FROM datainsamlere WHERE id = :id")->execute([':id' => $delete_id]);
    header("Location: prosjekt.php");
    exit;
}

if(isset($_POST['navn'])){
    $navn = $_POST['navn'];
    $epost = $_POST['epost'];
    $passord = password_hash($_POST['passord'], PASSWORD_DEFAULT);
    $prosjekt_id = $_SESSION['prosjekt_ID'];  // Her benyttes session-variabelen
    $prosjektleder = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO datainsamlere (navn, epost, passord, prosjekt_id, prosjektleder) VALUES (:navn, :epost, :passord, :prosjekt_id, :prosjektleder)");
    $stmt->execute([
        ':navn' => $navn,
        ':epost' => $epost,
        ':passord' => $passord,
        ':prosjekt_id' => $prosjekt_id,
        ':prosjektleder' => $prosjektleder

    ]);
    
    header("Location: prosjekt.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Datainsamlere</title>
</head>
<body>
    <h1>Prosjektinformasjon:</h1>
    <p>Prosjekt ID: <?php echo htmlspecialchars($_SESSION['prosjekt_ID']); ?></p>
    <p>Prosjektnavn: <?php echo htmlspecialchars($_SESSION['prosjektnavn']); ?></p>

    <h1>Legg til en ny datainnsamler for prosjektet <?php echo htmlspecialchars($_SESSION['prosjektnavn']); ?></h1>
    <form action="prosjekt.php" method="post">
        <input type="text" name="navn" placeholder="Navn" required>
        <input type="email" name="epost" placeholder="E-post" required>
        <input type="password" name="passord" placeholder="Passord" required>
        <input type="hidden" name="prosjekt_id" value="<?php echo htmlspecialchars($_SESSION['prosjekt_ID']); ?>">
        <input type="submit" value="Legg til">
    </form>

    <h1>Datainsamlere i prosjektet <?php echo htmlspecialchars($_SESSION['prosjektnavn']); ?></h1>
    <?php
    $prosjekt_id = $_SESSION['prosjekt_ID'];
    $stmt = $pdo->prepare("SELECT * FROM datainsamlere WHERE prosjekt_id = :prosjekt_id");
    $stmt->execute([':prosjekt_id' => $prosjekt_id]);
    while($row = $stmt->fetch()){
        echo '<p>'.htmlspecialchars($row['navn']).' - '.htmlspecialchars($row['epost']).' <a href="prosjekt.php?delete='. $row['id'] .'">Slett</a></p>';
    }
    ?>
</body>
</html>
