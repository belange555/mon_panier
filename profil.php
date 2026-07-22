<?php

require_once "includes/database.php";

session_start();

if (!isset($_SESSION["Utilisateur_id"])) {
    header("Location: connexion.php");
    exit();
}
// récupération des informations de l'utilisateur connecté

 $stmt = $connexion->prepare("
    SELECT nom, prenom, email, mot_de_passe
    FROM utilisateur
    WHERE Utilisateur_id = ?
");

$stmt->bind_param("i", $_SESSION["Utilisateur_id"]);

$stmt->execute(); 

$resultatUtilisateur = $stmt->get_result(); 

$utilisateur = $resultatUtilisateur->fetch_assoc();

if (isset($_POST["modifier_mot_de_passe"])) {

    $ancien = trim($_POST["ancien_mot_de_passe"]);
    
$nouveau = trim($_POST["nouveau_mot_de_passe"]);
$confirmation = trim($_POST["confirmation_mot_de_passe"]);

if ($ancien != $utilisateur["mot_de_passe"]) {

    $erreurMotDePasse = "L'ancien mot de passe est incorrect.";

    } elseif ($nouveau != $confirmation) {

    $erreurMotDePasse = "Les deux nouveaux mots de passe sont différents.";

}  else {


   $stmt = $connexion->prepare("
    UPDATE utilisateur
    SET mot_de_passe = ?
    WHERE Utilisateur_id = ?
");

    $stmt->bind_param(
    "si",
    $nouveau, 
    $_SESSION["Utilisateur_id"]
    );

    $stmt->execute();

    header("Location: profil.php?success=1");
    exit();
}
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["modifier_mot_de_passe"])) {
    
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);

   $stmt = $connexion->prepare("
SELECT nom, prenom, email, mot_de_passe
FROM utilisateur
WHERE Utilisateur_id = ?
");

    $stmt->bind_param(
        "sssi",
        $nom,
        $prenom,
        $email,
        $_SESSION["Utilisateur_id"]
    );

    $stmt->execute();

    header("Location: profil.php?success=1");
exit();
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mon profil</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header">
            <h2>Mon profil</h2>
        </div>

     <div class="card-body">

        <?php if (isset($_GET["success"])) : ?>

    <div class="alert alert-success">

        Votre profil a été mis à jour avec succès.

    </div>

<?php endif; ?>

<form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input
                        type="text"
                        name="nom"
                        class="form-control"
                        value="<?= htmlspecialchars($utilisateur["nom"]) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Prénom</label>
                    <input
                        type="text"
                        name="prenom"
                        class="form-control"
                        value="<?= htmlspecialchars($utilisateur["prenom"]) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($utilisateur["email"]) ?>">
                </div>

                <button class="btn btn-primary">
                    Enregistrer
                </button>

            </form>
            <!-- formulaire de modification du profil de l'utilisateur connecté -->

<hr>

<h4>Modifier le mot de passe</h4>

<form method="POST">

    <div class="mb-3">
        <label class="form-label">Ancien mot de passe</label>
        <input
            type="password"
            name="ancien_mot_de_passe"
            class="form-control"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nouveau mot de passe</label>
        <input
            type="password"
            name="nouveau_mot_de_passe"
            class="form-control"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirmer le nouveau mot de passe</label>
        <input
            type="password"
            name="confirmation_mot_de_passe"
            class="form-control"
            required>
    </div>

    <button
        type="submit"
        name="modifier_mot_de_passe"
        class="btn btn-warning">

        Modifier le mot de passe

    </button>

<?php if (isset($erreurMotDePasse)) { ?>
<div class="alert alert-danger">
    <?= $erreurMotDePasse ?>
</div>
<?php } ?>

</form>

        </div>

    </div>

</div>

</body>
</html>