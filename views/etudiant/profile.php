<?php 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header('Location: index.php?controller=auth&action=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Étudiant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      color: white;
      padding-top: 20px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      display: block;
      transition: all 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #495057;
      color: #ffc107;
    }
    .avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background-color: #6c757d;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      color: white;
      margin: 0 auto 15px;
    }
    .content {
      padding: 20px;
    }
    .card {
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
    .card-header {
      font-weight: bold;
    }
    .alert {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar p-0">
      <div class="text-center py-4 mb-3">
        <a href="index.php?controller=etudiant&action=profile">
          <div class="avatar"><?= strtoupper(substr($etudiant['nom'] ?? 'E', 0, 1)) ?></div>
          <h5 class="mb-0"><?= htmlspecialchars($etudiant['username'] ?? 'Étudiant') ?></h5>
        </a>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="index.php?controller=etudiant&action=dashboard" class="nav-link">
            <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php?controller=etudiant&action=profile" class="nav-link active">
            <i class="fas fa-user me-2"></i> Profil
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php?controller=auth&action=logout" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
          </a>
        </li>
      </ul>
    </div>

    <!-- Main content -->
    <div class="col-md-9 col-lg-10 content">
      <h2 class="mb-4">Mon Profil</h2>
      
      <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
          <?= $_SESSION['flash_message'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
      <?php endif; ?>

      <?php if (isset($etudiant) && is_array($etudiant)): ?>
        <div class="card">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-user-edit me-2"></i> Informations personnelles
          </div>
          <div class="card-body">
            <form method="POST" action="index.php?controller=etudiant&action=updateProfile">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="nom" class="form-label">Nom</label>
                  <input type="text" class="form-control" name="nom" id="nom" 
                         value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input type="text" class="form-control" name="prenom" id="prenom" 
                         value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" 
                       value="<?= htmlspecialchars($etudiant['user_email'] ?? $etudiant['email'] ?? '') ?>" required>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" 
                       value="<?= htmlspecialchars($etudiant['username'] ?? '') ?>" disabled>
                <div class="form-text">Le nom d'utilisateur ne peut pas être modifié.</div>
              </div>
              <div class="mb-3">
                <button type="submit" name="updateProfile" class="btn btn-primary">
                  <i class="fas fa-save me-1"></i> Mettre à jour le profil
                </button>
              </div>
            </form>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header bg-info text-white">
            <i class="fas fa-info-circle me-2"></i> Informations académiques
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Date d'inscription</label>
                <p class="form-control-static">
                  <?= isset($etudiant['created_at']) ? date('d/m/Y', strtotime($etudiant['created_at'])) : 'Non disponible' ?>
                </p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Classe/Filière</label>
                <p class="form-control-static">
                  <?= htmlspecialchars($etudiant['classe'] ?? 'Non spécifiée') ?>
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Thème soumis</label>
                <p class="form-control-static">
                  <?= $etudiant['theme'] ? htmlspecialchars($etudiant['theme']) : 'Aucun thème soumis' ?>
                </p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Encadreur assigné</label>
                <p class="form-control-static">
                  <?php if (isset($etudiant['encadreur_nom']) && $etudiant['encadreur_nom']): ?>
                    <?= htmlspecialchars($etudiant['encadreur_nom'] . ' ' . $etudiant['encadreur_prenom']) ?>
                  <?php else: ?>
                    <span class="text-warning">Aucun encadreur assigné</span>
                  <?php endif; ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle me-2"></i> Erreur : Les informations de votre profil sont introuvables.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>