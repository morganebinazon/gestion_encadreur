<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion d'Encadreurs</title>
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
        .login-container {
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
            background-color: #3b5998;
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
        .btn-primary {
            background-color: #3b5998;
            border-color: #3b5998;
        }
        .btn-primary:hover {
            background-color: #2d4373;
            border-color: #2d4373;
        }
        .form-control:focus {
            border-color: #3b5998;
            box-shadow: 0 0 0 0.25rem rgba(59, 89, 152, 0.25);
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="card-img h-100">
                    <div class="text-center mb-4">
                        <h2><i class="fas fa-graduation-cap me-2"></i> Gestion des Encadreurs</h2>
                    </div>
                    <h3 class="mb-4">Plateforme d'Encadrement de Projets</h3>
                    <p class="lead">Connectez-vous pour accéder à votre espace personnel et suivre l'avancement de vos projets.</p>
                    <div class="mt-4">
                        <p><i class="fas fa-check-circle me-2"></i> Suivi personnalisé des étudiants</p>
                        <p><i class="fas fa-check-circle me-2"></i> Gestion simplifiée des encadrements</p>
                        <p><i class="fas fa-check-circle me-2"></i> Communication directe étudiant-encadreur</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Connexion</h2>
                    
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show mb-4">
                            <?= $_SESSION['flash_message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['flash_message']); ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="index.php?controller=auth&action=login" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mot de passe
                            </label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p>Vous n'avez pas de compte ? 
                                <a href="index.php?controller=auth&action=register" class="text-decoration-none">Créer un compte</a>
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