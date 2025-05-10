<?php if (!isset($_SESSION['etudiant_id'])) {
    echo "Erreur : Étudiant non connecté.";
    exit();
}?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord Étudiant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
    }
    .sidebar a:hover {
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
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar p-4">
      <div class="text-center mb-4">
        <a href="index.php?controller=etudiant&action=profile">
          <div class="avatar mx-auto mb-2"><?= strtoupper($etudiant['nom'][0]) ?></div>
        </a>
        <h5><?= htmlspecialchars(($etudiant['username'])) ?></h5>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a href="index.php?controller=etudiant&action=notifications" class="nav-link">🔔 Notifications</a></li>
        <li class="nav-item mb-2"><a href="index.php?controller=etudiant&action=profile" class="nav-link">👤 Profil</a></li>
        <li class="nav-item"><a href="index.php?controller=auth&action=logout" class="nav-link text-danger">🚪 Déconnexion</a></li>
      </ul>
    </div>

    <!-- Main content -->
    <div class="col-md-9 p-5">
      <h2 class="mb-4">Bienvenue sur votre espace étudiant</h2>

      <!-- Notification encadreur -->
      <?php if ($etudiant['encadreur_nom']): ?>
        <div class="alert alert-success">
          ✅ Un encadreur vous a été attribué : <strong><?= htmlspecialchars($etudiant['encadreur_nom']) ?> <?= htmlspecialchars($etudiant['encadreur_prenom']) ?></strong>
        </div>
      <?php else: ?>
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
          ⚠️ Aucun encadreur attribué pour le moment.
          <form method="post" action="index.php?controller=etudiant&action=relancer">
            <button type="submit" name="relancer" class="btn btn-outline-danger btn-sm">Relancer l’administration</button>
          </form>
        </div>
      <?php endif; ?>

      <!-- Formulaire de soumission -->
      <div class="card">
        <div class="card-header bg-primary text-white">Soumission de votre cahier de charges</div>
        <div class="card-body">
        <?php if (isset($etudiant['form_submitted']) && $etudiant['form_submitted']): ?>
            <div class="alert alert-info">Vous avez déjà soumis votre formulaire.</div>
          <?php else: ?>
            <form method="POST" action="index.php?controller=etudiant&action=submitForm" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="theme" class="form-label">Thème de soutenance</label>
                <input type="text" class="form-control" name="theme" id="theme" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Avez-vous un binôme ?</label><br>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="binome" value="1" id="binomeOui" required>
                  <label class="form-check-label" for="binomeOui">Oui</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="binome" value="0" id="binomeNon" required>
                  <label class="form-check-label" for="binomeNon">Non</label>
                </div>
              </div>
              <div class="mb-3">
                <label for="fichier" class="form-label">Cahier des charges (PDF uniquement)</label>
                <input type="file" class="form-control" name="fichier" id="fichier" accept="application/pdf" required>
              </div>
              <button type="submit" name="submitForm" class="btn btn-success">Soumettre</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
