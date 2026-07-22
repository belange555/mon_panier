<?php 

require_once "includes/database.php";

session_start();

$message = "";

if (!isset($_SESSION["Utilisateur_id"])) {

   header("Location: connexion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom_liste = trim($_POST["nom_liste"]);

    if (empty($nom_liste)) {

        $message = "Veuillez saisir un nom de liste.";

    } else {

        $stmt = $connexion->prepare("
            INSERT INTO liste (nom, Utilisateur_id)
            VALUES (?, ?)
        ");

        $stmt->bind_param(
            "si",
            $nom_liste,
            $_SESSION["Utilisateur_id"]
        );

        if ($stmt->execute()) {

            $message = "Liste créée avec succès.";

        } else {

            $message = "Erreur : " . $stmt->error;

        }

    }

}


?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Mon Panier</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

<body class="bg-light">
    <div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow mb-4">

<div class="card-body">

<h2 class="mb-2">
Bonjour <?php echo ucfirst(htmlspecialchars($_SESSION["prenom"])); ?> 👋
</h2>

<p class="text-muted">
Bienvenue sur Mon Panier
</p>

</div>

</div>

<div class="card shadow mb-4">

<div class="card-body">

<h4 class="mb-3">

📝 Créer une nouvelle liste

</h4>

<?php if ($message != "") { ?>

<div class="alert alert-info">

<?php echo $message; ?>

</div>

<?php } ?>

<form action="dashboard.php" method="POST">

<div class="mb-3">

<label class="form-label">

Nom de la liste

</label>

<input
type="text"
name="nom_liste"
class="form-control"
required>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-primary">

Créer la liste

</button>

</div>

</form>

</div>

</div>

<div class="card shadow">

<div class="card-body">

<h4 class="mb-4">

🛒 Mes listes

</h4>

<?php

$stmt = $connexion->prepare("
SELECT Liste_id, nom
FROM liste
WHERE Utilisateur_id = ?
ORDER BY date_creation DESC
");

$stmt->bind_param("i", $_SESSION["Utilisateur_id"]);
$stmt->execute();
$resultat = $stmt->get_result();

if ($resultat->num_rows == 0) {
    echo "<p class='text-muted'>Aucune liste créée.</p>";
}

while ($liste = $resultat->fetch_assoc()) {
?>

<div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3">

<strong>

<?php echo htmlspecialchars($liste["nom"]); ?>

</strong>

<a
href="liste.php?id=<?php echo $liste["Liste_id"]; ?>"
class="btn btn-outline-primary">

Ouvrir

</a> 
</div>

<?php } ?>
</div> 
</div>  

<div class="text-center mt-4">

<a href="stock.php" class="btn btn-success">
📦 Mon stock
</a>

<a href="profil.php" class="btn btn-warning">
👤 Mon profil
</a>

<a href="deconnexion.php" class="btn btn-danger">
Déconnexion
</a>

</div>

</div>
</body> 
</html>