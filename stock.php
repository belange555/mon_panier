<?php

require_once "includes/database.php";

session_start();

echo "Utilisateur connecté : " . $_SESSION["Utilisateur_id"];
if (!isset($_SESSION["Utilisateur_id"])) {

    header("Location: connexion.php");
    exit();

}

?>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Stock</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">
    <div class="card shadow mb-4">

<div class="card-body">

<h2>

📦 Mon stock

</h2>

<p class="text-muted">

Consultez les produits disponibles.

</p>

</div>

</div>

<div class="card shadow">

<div class="card-body">

<h4 class="mb-4">

📋 Produits en stock

</h4>
<?php   
$stmt = $connexion->prepare("
SELECT
produit.nom,
stock.quantite,
stock.date_expiration
FROM stock
INNER JOIN produit
ON stock.Produit_id = produit.Produit_id
WHERE stock.Utilisateur_id = ?
ORDER BY produit.nom
");

$stmt->bind_param("i", $_SESSION["Utilisateur_id"]);

$stmt->execute();

$resultat = $stmt->get_result();

while ($stock = $resultat->fetch_assoc()) {

?>

<div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3">

<div>

<strong>

<?php echo htmlspecialchars($stock["nom"]); ?>

</strong>

<br>

Quantité :

<?php echo $stock["quantite"]; ?>

<?php

if ($stock["date_expiration"] != NULL) {

?>

<br>

<small class="text-muted">

À consommer avant :

<?php echo $stock["date_expiration"]; ?>

</small>

<?php

}

?>

</div>

</div>

<?php

}

?>

</div>

</div>

<div class="text-center mt-4">

<a
href="dashboard.php"
class="btn btn-secondary">

← Retour

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>