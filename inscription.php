<?php
require_once "includes/database.php";

$message = "";
$message_succes = "";

$nom = "";
$prenom = "";
$email = "";
$mot_de_passe = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);

    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {

        $message = "Tous les champs sont obligatoires.";

    } 
    else {
        // Ici viendra la vérification de l'email
        
    $stmt = $connexion->prepare("SELECT Utilisateur_id FROM utilisateur WHERE email = ?");

$stmt->bind_param("s", $email);

$stmt->execute();

$resultat = $stmt->get_result();


if ($resultat->num_rows > 0) {

    $message = "Cet email est déjà utilisé.";

}

 else {

    $stmt = $connexion->prepare("
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "ssss",
    $nom,
    $prenom,
    $email,
    $mot_de_passe);

if ($stmt->execute()) {

   $message_succes = "Compte créé avec succès ! Redirection vers la connexion...";
   
   header("Refresh:2; url=connexion.php");
        exit();

} 
else {

    $message = "Erreur : " . $stmt->error;
    
}


} //ferme le else de la vérification de l'email

    } //ferme le else de la vérification des champs vides
} //ferme le if ($_SERVER["REQUEST_METHOD"] == "POST")

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

<body class="bg-light">

<div class="container">

<div class="row justify-content-center align-items-center vh-100">

<div class="col-lg-5 col-md-6">

<div class="card shadow">

<div class="card-body p-5">


<h2 class="text-center mb-4">

🛒 Mon Panier

</h2>

<p class="text-center text-muted">

Créer un compte
</p>

<?php
if ($message != "") {
?>
<div class="alert alert-danger">

<?php echo $message; ?>

</div>
<?php
}
?>

<?php
if ($message_succes != "") {
?>
<div class="alert alert-success">

<?php echo $message_succes; ?>

</div>
<?php
}
?>

<form action="inscription.php" method="POST">

    <div class="mb-3">

<label class="form-label">

Prénom

</label>

<input
value="<?php echo htmlspecialchars($prenom); ?>"
type="text"
name="prenom"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Nom

</label>

<input
value="<?php echo htmlspecialchars($nom); ?>"
type="text"
name="nom"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
value="<?php echo htmlspecialchars($email); ?>"
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Mot de passe

</label>

<input
value="<?php echo htmlspecialchars($mot_de_passe); ?>"
type="password"
name="mot_de_passe"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-success w-100">

Créer mon compte

</button>

<div class="text-center mt-3">

Vous avez déjà un compte ?

<br>

<a href="connexion.php">

Se connecter

</a>

</div>

</form>

</div>
</div>
</div>
</div>
</div>  

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>