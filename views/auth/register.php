<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion d'Encadreurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            max-width: 900px;
            width: 100%;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .card-img {
            height: 100%;
            object-fit: cover;
            border-radius: 0;
            background-color: #28a745;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            padding: 2rem;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 1rem;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
    <div class="container register-container">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="card-img h-100">
                    <div class="text-center mb-4">
                        <!-- Si vous avez un logo, remplacez ce texte par une balise img -->
                        <h2><i class="fas fa-graduation-cap me-2"></i> Gestion des Encadreurs</h2>
                    </div>
                    <h3 class="mb-4">Créez votre compte étudiant</h3>
                    <p class="lead">Rejoignez notre plateforme pour bénéficier d'un suivi personnalisé de vos projets.</p>
                    <div class="mt-4">
                        <p><i class="fas fa-check-circle me-2"></i> Accès à des encadreurs qualifiés</p>
                        <p><i class="fas fa-check-circle me-2"></i> Soumission de thèmes de projet</p>
                        <p><i class="fas fa-check-circle me-2"></i> Suivi de progression en temps réel</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Inscription</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?controller=auth&action=register">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Nom d'utilisateur
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mot de passe
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Le mot de passe doit contenir au moins 8 caractères.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-check-circle me-2"></i>Confirmer le mot de passe
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>S'inscrire
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p>Vous avez déjà un compte ? 
                                <a href="index.php?controller=auth&action=login" class="text-decoration-none">Se connecter</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>