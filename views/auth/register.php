<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row shadow rounded bg-white">
            <div class="col-md-6 d-none d-md-block">
                <img src="public/assets/logo.png" class="img-fluid h-100 w-100" alt="Logo">
            </div>
            <div class="col-md-6 p-4">
                <h3>Inscription Étudiant</h3>
                <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Nom d’utilisateur</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">S'inscrire</button>
                    <p class="mt-2 text-center">Déjà un compte ? <a href="index.php?controller=auth&action=login">Connexion</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
