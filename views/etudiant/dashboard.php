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
  <title>Tableau de bord Étudiant</title>
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
          <a href="index.php?controller=etudiant&action=dashboard" class="nav-link active">
            <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
          </a>
        </li>
        <li class="nav-item">
          <a href="index.php?controller=etudiant&action=profile" class="nav-link">
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
      <h2 class="mb-4">Bienvenue sur votre espace étudiant</h2>
      
      <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
          <?= $_SESSION['flash_message'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
      <?php endif; ?>

      <!-- Notification encadreur -->
      <?php if (isset($etudiant['encadreur_nom']) && $etudiant['encadreur_nom']): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle me-2"></i> Un encadreur vous a été attribué : 
          <strong><?= htmlspecialchars($etudiant['encadreur_nom']) ?> <?= htmlspecialchars($etudiant['encadreur_prenom']) ?></strong>
        </div>
      <?php else: ?>
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
          <div>
            <i class="fas fa-exclamation-triangle me-2"></i> Aucun encadreur ne vous a été attribué pour le moment.
          </div>
          <form method="post" action="index.php?controller=etudiant&action=relancer">
            <button type="submit" name="relancer" class="btn btn-outline-danger btn-sm">
              <i class="fas fa-bell me-1"></i> Relancer l'administration
            </button>
          </form>
        </div>
      <?php endif; ?>

      <!-- Formulaire de soumission -->
      <div class="card">
        <div class="card-header bg-primary text-white">
          <i class="fas fa-file-upload me-2"></i> Soumission de votre cahier de charges
        </div>
        <div class="card-body">
          <?php if (isset($etudiant['form_submitted']) && $etudiant['form_submitted']): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i> Vous avez déjà soumis votre formulaire le 
              <?= date('d/m/Y à H:i', strtotime($etudiant['date_soumission'] ?? 'now')) ?>.
              
              <?php if (isset($etudiant['fichier_pdf']) && $etudiant['fichier_pdf']): ?>
                <div class="mt-3">
                  <strong>Thème:</strong> <?= htmlspecialchars($etudiant['theme'] ?? 'Non spécifié') ?><br>
                  <strong>Binôme:</strong> <?= $etudiant['binome'] ? 'Oui' : 'Non' ?><br>
                  <strong>Fichier:</strong> 
                  <a href="public/uploads/<?= htmlspecialchars($etudiant['fichier_pdf']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-download me-1"></i> Télécharger le fichier
                  </a>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <form method="POST" action="index.php?controller=etudiant&action=submitForm" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="theme" class="form-label">Thème de soutenance</label>
                <input type="text" class="form-control" name="theme" id="theme" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Avez-vous un binôme ?</label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="binome" value="1" id="binomeOui" required>
                  <label class="form-check-label" for="binomeOui">Oui</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="binome" value="0" id="binomeNon" required>
                  <label class="form-check-label" for="binomeNon">Non</label>
                </div>
              </div>
              <div class="mb-3">
                <label for="fichier" class="form-label">Cahier des charges (PDF uniquement)</label>
                <input type="file" class="form-control" name="fichier" id="fichier" accept="application/pdf" required>
                <div class="form-text">Taille maximale: 5 Mo</div>
              </div>
              <button type="submit" name="submitForm" class="btn btn-success">
                <i class="fas fa-paper-plane me-1"></i> Soumettre
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Informations générales -->
      <div class="card">
        <div class="card-header bg-info text-white">
          <i class="fas fa-info-circle me-2"></i> Informations générales
        </div>
        <div class="card-body">
          <p>Complétez votre profil pour faciliter le travail de votre encadreur. Si vous rencontrez des difficultés, n'hésitez pas à contacter l'administration.</p>
          <p>Dates importantes:</p>
          <ul>
            <li><strong>Date limite de soumission du cahier des charges:</strong> 30 juin 2025</li>
            <li><strong>Début des soutenances:</strong> 15 juillet 2025</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>