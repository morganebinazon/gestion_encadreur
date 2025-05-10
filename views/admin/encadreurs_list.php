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
    <title>Liste des Encadreurs - Administration</title>
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
        .btn-actions {
            white-space: nowrap;
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
                    <h2><i class="fas fa-users me-2"></i> Liste des Encadreurs</h2>
                    <a href="index.php?controller=admin&action=addEncadreur" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter un encadreur
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Compétences</th>
                                        <th>Email</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($encadreurs)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun encadreur enregistré</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($encadreurs as $encadreur): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($encadreur['nom']) ?></td>
                                                <td><?= htmlspecialchars($encadreur['prenom']) ?></td>
                                                <td>
                                                    <?php 
                                                    $competences = explode(',', $encadreur['competences']);
                                                    foreach ($competences as $competence): ?>
                                                        <span class="badge bg-info"><?= htmlspecialchars($competence) ?></span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><?= htmlspecialchars($encadreur['email'] ?? 'Non défini') ?></td>
                                                <td class="text-center btn-actions">
                                                    <a href="index.php?controller=admin&action=editEncadreur&id=<?= $encadreur['id'] ?>" 
                                                       class="btn btn-warning btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?controller=admin&action=assignForm&id=<?= $encadreur['id'] ?>" 
                                                       class="btn btn-info btn-sm" title="Assigner un étudiant">
                                                        <i class="fas fa-user-plus"></i>
                                                    </a>
                                                    <a href="index.php?controller=admin&action=deleteEncadreur&id=<?= $encadreur['id'] ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet encadreur ?');" 
                                                       title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                                <h5>Total des encadreurs</h5>
                                <p class="h3"><?= count($encadreurs) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="index.php?controller=admin&action=assignedStudents" class="text-decoration-none">
                                    <i class="fas fa-user-check fa-3x mb-3 text-success"></i>
                                    <h5>Étudiants assignés</h5>
                                    <p class="h3">
                                        <?php 
                                        $db = Database::getConnection();
                                        $stmt = $db->query("SELECT COUNT(*) FROM affectations");
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="index.php?controller=admin&action=unassignedStudents" class="text-decoration-none">
                                    <i class="fas fa-user-times fa-3x mb-3 text-danger"></i>
                                    <h5>Étudiants non assignés</h5>
                                    <p class="h3">
                                        <?php 
                                        $db = Database::getConnection();
                                        $stmt = $db->query("SELECT COUNT(*) FROM etudiants WHERE id NOT IN (SELECT etudiant_id FROM affectations)");
                                        echo $stmt->fetchColumn();
                                        ?>
                                    </p>
                                </a>
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