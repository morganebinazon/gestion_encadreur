<!-- views/auth/login.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row shadow rounded bg-white overflow-hidden">
            <div class="col-md-6 d-none d-md-block">
                <img src="public/assets/logo.png" class="img-fluid h-100 w-100" alt="Logo" style="object-fit: cover;">
            </div>
            <div class="col-md-6 p-4">
                <h3 class="mb-4">Connexion</h3>
                <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form action="index.php?controller=auth&action=login" method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100">Se connecter</button>
                    <p class="mt-3 text-center">Pas encore inscrit ? 
                        <a href="index.php?controller=auth&action=register">Créer un compte</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
