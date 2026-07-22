<?php

require_once "includes/database.php";

session_start();

/* vérification de la connexion */

if (!isset($_SESSION["Utilisateur_id"])) {

    header("Location: connexion.php");
    exit();

}

/* vérification de la liste_id */

if (!isset($_GET["id"])) {

    header("Location: dashboard.php");
    exit();

}

$liste_id = (int) $_GET["id"];

$stmt = $connexion->prepare("
SELECT nom
FROM liste
WHERE Liste_id = ?
AND Utilisateur_id = ?
");

// recupération de la liste  appartenant à l'utilisateur  

$stmt->bind_param(
    "ii",
    $liste_id,
    $_SESSION["Utilisateur_id"]
);

$stmt->execute();

$resultatListe = $stmt->get_result();

$liste = $resultatListe->fetch_assoc();

/*vérification ajouter produit (achat)*/
if (!$liste) {

    header("Location: dashboard.php");
    exit();

}

    if (isset($_POST["ligne_id"])) {

    $ligne_id = (int) $_POST["ligne_id"];

    $stmt = $connexion->prepare("
        SELECT Produit_id, quantite
        FROM ligneliste
        WHERE LigneListe_id = ?
    ");

    $stmt->bind_param("i", $ligne_id);

    $stmt->execute();

    $resultatLigne = $stmt->get_result();

    $ligne = $resultatLigne->fetch_assoc();

    $produit_id = $ligne["Produit_id"];
    $quantite = $ligne["quantite"];

    $stmt = $connexion->prepare("
    SELECT Stock_id, quantite
    FROM stock
    WHERE Produit_id = ?
    AND Utilisateur_id = ?
    ");

$stmt->bind_param(
    "ii",
    $produit_id,
    $_SESSION["Utilisateur_id"]
);

$stmt->execute();

$resultatStock= $stmt->get_result();

if ($resultatStock->num_rows > 0) {

    $stock = $resultatStock->fetch_assoc();

    $stmt = $connexion->prepare("
        UPDATE stock
        SET quantite = quantite + ?
        WHERE Stock_id = ?
    ");

    $stmt->bind_param(
        "ii",
        $quantite,
        $stock["Stock_id"]
    );

    $stmt->execute();
    
}
else {

    $stmt = $connexion->prepare("
        INSERT INTO stock (Utilisateur_id, Produit_id, quantite)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "iii",
        $_SESSION["Utilisateur_id"],
        $produit_id,
        $quantite
    );

    $stmt->execute();

} 
// ajout du produit au stock de l'utilisateur


// cocher ou décocher un produit de la liste

$stmt = $connexion->prepare("
    DELETE FROM ligneliste
    WHERE LigneListe_id = ?
");

$stmt->bind_param(
    "i",
    $ligne_id
);

$stmt->execute();

header("Location: liste.php?id=" . $liste_id);
    exit();
      }
if (isset($_POST["supprimer_ligne_id"])) {

    $ligne_id = (int) $_POST["supprimer_ligne_id"];

    $stmt = $connexion->prepare("
        DELETE FROM ligneliste
        WHERE LigneListe_id = ?
    ");

    $stmt->bind_param(
        "i",
        $ligne_id
    );

    $stmt->execute();

    header("Location: liste.php?id=" . $liste_id);
exit();
}
// suppression d'une ligne de la liste


if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $nom_produit = ucfirst(strtolower(trim($_POST["nom_produit"])));
    $quantite = (int) $_POST["quantite"];

$stmt = $connexion->prepare("
SELECT Produit_id
FROM produit
WHERE nom = ?
");

$stmt->bind_param(
    "s",
    $nom_produit
);

$stmt->execute();

$resultat = $stmt->get_result();

if ($resultat->num_rows == 0) {

    $categorie = "Divers";

    $stmt = $connexion->prepare("
        INSERT INTO produit (nom, categorie)
        VALUES (?, ?)
    ");

    $stmt->bind_param(
        "ss",
        $nom_produit,
        $categorie
    );

    $stmt->execute();

    $produit_id = $connexion->insert_id;

} else {

    $produit = $resultat->fetch_assoc();

    $produit_id = $produit["Produit_id"];

} // vérification de l'existence du produit


 $stmt = $connexion->prepare("
        SELECT LigneListe_id, quantite
        FROM ligneliste
        WHERE Liste_id = ?
        AND Produit_id = ?
    ");

$stmt->bind_param(
    "ii",
    $liste_id,
    $produit_id
);
$stmt->execute();

$resultat = $stmt->get_result();

if ($resultat->num_rows > 0) {

    $ligne = $resultat->fetch_assoc();

    $stmt = $connexion->prepare("
        UPDATE ligneliste
        SET quantite = quantite + ?
        WHERE LigneListe_id = ?
    ");

    $stmt->bind_param(
        "ii",
        $quantite,
        $ligne["LigneListe_id"]
    );

    $stmt->execute();

} else {

    $stmt = $connexion->prepare("
        INSERT INTO ligneliste (Liste_id, Produit_id, quantite, achete)
        VALUES (?, ?, ?, 0)
    ");

    $stmt->bind_param(
        "iii",
        $liste_id,
        $produit_id,
        $quantite
    );

    $stmt->execute();

} // cocher l'existence du produit dans la liste

header("Location: liste.php?id=" . $liste_id);
exit();
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

<h2>

🛒 <?php echo htmlspecialchars($liste["nom"]); ?>

</h2>

<p class="text-muted">

Gérez les produits de votre liste.

</p>

</div>

</div> <!-- affichage du nom de la liste -->

<?php
$stmt = $connexion->prepare("
SELECT
ligneliste.LigneListe_id,
produit.nom,
ligneliste.quantite,
ligneliste.achete
FROM ligneliste
INNER JOIN produit
ON ligneliste.Produit_id = produit.Produit_id
WHERE ligneliste.Liste_id = ?
"); 
//requete pour afficher les produits (nom, quantité, achete) de la liste

$stmt->bind_param("i", $liste_id);

$stmt->execute();

$produits = $stmt->get_result();


// on a supprimé le debug echo "liste:" . $liste_id;
?>

<div class="card shadow mb-4">

<div class="card-body">

<h4 class="mb-4">

➕ Ajouter un produit

</h4>

<form action="liste.php?id=<?php echo $liste_id; ?>" method="POST">

<div class="mb-3">

<label class="form-label">

Nom du produit

</label>

<input
type="text"
name="nom_produit"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Quantité

</label>

<input
type="number"
name="quantite"
class="form-control"
value="1"
min="1"
required>

</div>

<div class="d-grid">

<button
type="submit"
class="btn btn-success">

Ajouter

</button>

</div>

</form>

</div>

</div>

<div class="card shadow">

<div class="card-body">

<h4 class="mb-4">

🛒 Produits

</h4>

<?php while ($produit = $produits->fetch_assoc()) { ?>

<div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3">

<div>

<form
method="POST"
action="liste.php?id=<?php echo $liste_id; ?>"
class="d-inline">

<input
type="hidden"
name="ligne_id"
value="<?php echo $produit["LigneListe_id"]; ?>">

<button
type="submit"
class="btn btn-success btn-sm">

Acheter

</button>

<strong>

<?php echo htmlspecialchars($produit["nom"]); ?>

</strong>

<span class="badge bg-secondary ms-2">

x <?php echo $produit["quantite"]; ?>

</span>

</form>

</div>

<form
method="POST"
action="liste.php?id=<?php echo $liste_id; ?>">

<input
type="hidden"
name="supprimer_ligne_id"
value="<?php echo $produit["LigneListe_id"]; ?>">

<button
type="submit"
class="btn btn-outline-danger btn-sm"
onclick="return confirm('Supprimer ce produit ?')">

Supprimer

</button>

</form>

</div>

<?php } ?>
<!-- boucle pour afficher les produits de la liste avec la case à cocher -->
 

<div class="text-center mt-4">

<a
href="dashboard.php"
class="btn btn-secondary">

← Retour au tableau de bord

</a>

</div>
</div>

</div>

</div>
</body>

</html>