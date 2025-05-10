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
    <title>Étudiants Non Assignés - Administration</title>
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
                        <a href="index.php?controller=admin&action=encadreurs" class="nav-link">
                            <i class="fas fa-users me-2"></i> Encadreurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=admin&action=assignedStudents" class="nav-link">
                            <i class="fas fa-user-check me-2"></i> Étudiants assignés
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?controller=admin&action=unassignedStudents" class="nav-link active">
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
                    <h2><i class="fas fa-user-times me-2"></i> Étudiants Non Assignés</h2>
                    <div>
                        <a href="index.php?controller=admin&action=assignedStudents" class="btn btn-outline-success me-2">
                            <i class="fas fa-user-check me-1"></i> Voir les étudiants assignés
                        </a>
                        <a href="index.php?controller=admin&action=encadreurs" class="btn btn-secondary">
                            <i class="fas fa-users me-1"></i> Gérer les encadreurs
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-info alert-dismissible fade show mb-4">
                        <?= $_SESSION['flash_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i> Tous les étudiants sont assignés à des encadreurs.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                            <th>Classe</th>
                                            <th>Date d'inscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($student['nom']) ?></td>
                                                <td><?= htmlspecialchars($student['prenom']) ?></td>
                                                <td><?= htmlspecialchars($student['email'] ?? 'Non défini') ?></td>
                                                <td><?= htmlspecialchars($student['classe'] ?? 'Non définie') ?></td>
                                                <td>
                                                    <?= isset($student['created_at']) 
                                                        ? date('d/m/Y', strtotime($student['created_at'])) 
                                                        : 'Non disponible' 
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-success" 
                                                       data-bs-toggle="modal" data-bs-target="#assignEncadreurModal<?= $student['id'] ?>"
                                                       title="Assigner un encadreur">
                                                        <i class="fas fa-user-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal pour assigner un encadreur -->
                                            <div class="modal fade" id="assignEncadreurModal<?= $student['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                Assigner un encadreur à <?= htmlspecialchars($student['prenom'] . ' ' . $student['nom']) ?>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="index.php?controller=admin&action=saveAffectation">
                                                                <input type="hidden" name="etudiant_id" value="<?= $student['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="encadreur_id_<?= $student['id'] ?>" class="form-label">Sélectionner un encadreur</label>
                                                                    <select class="form-select" name="encadreur_id" id="encadreur_id_<?= $student['id'] ?>" required>
                                                                        <option value="">Choisir un encadreur</option>
                                                                        <?php 
                                                                        $db = Database::getConnection();
                                                                        $stmt = $db->query("SELECT * FROM encadreurs ORDER BY nom, prenom");
                                                                        $encadreurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                        
                                                                        foreach ($encadreurs as $encadreur):
                                                                        ?>
                                                                            <option value="<?= $encadreur['id'] ?>">
                                                                                <?= htmlspecialchars($encadreur['prenom'] . ' ' . $encadreur['nom']) ?>
                                                                                <?php if (!empty($encadreur['competences'])): ?>
                                                                                    (<?= htmlspecialchars($encadreur['competences']) ?>)
                                                                                <?php endif; ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="d-grid">
                                                                    <button type="submit" class="btn btn-success">
                                                                        <i class="fas fa-user-plus me-1"></i> Assigner l'encadreur
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($students)): ?>
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle me-2"></i> Affectation automatique
                    </div>
                    <div class="card-body">
                        <p>Vous pouvez assigner automatiquement des encadreurs aux étudiants en fonction de leurs compétences et de la charge de travail.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> L'affectation automatique prend en compte les compétences des encadreurs et tente de répartir équitablement la charge de travail.
                        </div>
                        <form method="POST" action="index.php?controller=admin&action=autoAssign">
                            <div class="row align-items-end">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre maximum d'étudiants par encadreur</label>
                                    <input type="number" name="max_students" class="form-control" min="1" max="20" value="5">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-warning" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir lancer l\'affectation automatique ?');">
                                            <i class="fas fa-magic me-1"></i> Lancer l'affectation automatique
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-filter me-2"></i> Statistiques
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Étudiants non assignés</h5>
                                        <p class="display-5 text-danger"><?= count($students) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Encadreurs disponibles</h5>
                                        <?php
                                        $db = Database::getConnection();
                                        $stmt = $db->query("SELECT COUNT(*) FROM encadreurs");
                                        $encadreurs_count = $stmt->fetchColumn();
                                        ?>
                                        <p class="display-5 text-primary"><?= $encadreurs_count ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Relances reçues</h5>
                                        <?php
                                        $db = Database::getConnection();
                                        $stmt = $db->query("SELECT COUNT(*) FROM relances");
                                        $relances_count = $stmt->fetchColumn() ?? 0;
                                        ?>
                                        <p class="display-5 text-warning"><?= $relances_count ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>