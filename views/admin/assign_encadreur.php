<?php 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?controller=auth&action=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigner un Étudiant - Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
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
        .content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="text-center py-4 mb-3">
                    <h5 class="text-light mb-0">Administration</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="index.php?controller=admin&action=encadreurs" class="nav-link active">
                            <i class="fas fa-users me-2"></i> Encadreurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=admin&action=assignedStudents" class="nav-link">
                            <i class="fas fa-user-check me-2"></i> Étudiants assignés
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=admin&action=unassignedStudents" class="nav-link">
                            <i class="fas fa-user-times me-2"></i> Étudiants non assignés
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-user-plus me-2"></i> 
                        Assigner un étudiant à l'encadreur : 
                        <?= htmlspecialchars($encadreur['prenom']) ?> <?= htmlspecialchars($encadreur['nom']) ?>
                    </h2>
                    <a href="index.php?controller=admin&action=encadreurs" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour aux encadreurs
                    </a>
                </div>

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-info alert-dismissible fade show mb-4">
                        <?= $_SESSION['flash_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-2"></i> Informations sur l'encadreur
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nom :</strong> <?= htmlspecialchars($encadreur['prenom']) ?> <?= htmlspecialchars($encadreur['nom']) ?></p>
                                <p><strong>Compétences :</strong> 
                                    <?php 
                                    $competences = explode(',', $encadreur['competences']);
                                    foreach ($competences as $competence): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($competence) ?></span>
                                    <?php endforeach; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email :</strong> <?= htmlspecialchars($encadreur['email'] ?? 'Non défini') ?></p>
                                <p><strong>Étudiants actuellement assignés :</strong> 
                                    <?php 
                                    $db = Database::getConnection();
                                    $stmt = $db->prepare("SELECT COUNT(*) FROM affectations WHERE encadreur_id = ?");
                                    $stmt->execute([$encadreur['id']]);
                                    echo $stmt->fetchColumn();
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user-plus me-2"></i> Assigner un étudiant
                    </div>
                    <div class="card-body">
                        <?php if (empty($etudiants)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Tous les étudiants sont déjà assignés à des encadreurs.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="index.php?controller=admin&action=saveAffectation">
                                <input type="hidden" name="encadreur_id" value="<?= $encadreur['id'] ?>">
                                
                                <div class="mb-3">
                                    <label for="etudiant_id" class="form-label">Choisir un étudiant</label>
                                    <select name="etudiant_id" id="etudiant_id" class="form-select" required>
                                        <option value="">Sélectionner un étudiant</option>
                                        <?php foreach ($etudiants as $etudiant): ?>
                                            <option value="<?= $etudiant['id'] ?>">
                                                <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
                                                <?php if (isset($etudiant['email']) && !empty($etudiant['email'])): ?>
                                                    (<?= htmlspecialchars($etudiant['email']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-user-plus me-1"></i> Affecter l'étudiant
                                    </button>
                                    <a href="index.php?controller=admin&action=encadreurs" class="btn btn-secondary btn-lg">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>