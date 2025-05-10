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
    <title>Étudiants Assignés - Administration</title>
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
                        <a href="index.php?controller=admin&action=assignedStudents" class="nav-link active">
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
                    <h2><i class="fas fa-user-check me-2"></i> Étudiants Assignés</h2>
                    <div>
                        <a href="index.php?controller=admin&action=unassignedStudents" class="btn btn-outline-primary me-2">
                            <i class="fas fa-user-times me-1"></i> Voir les étudiants non assignés
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
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucun étudiant n'a été assigné à un encadreur pour le moment.
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
                                            <th>Encadreur</th>
                                            <th>Date d'affectation</th>
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
                                                    <?= htmlspecialchars($student['encadreur_nom'] . ' ' . $student['encadreur_prenom']) ?>
                                                </td>
                                                <td>
                                                    <?= isset($student['date_affectation']) 
                                                        ? date('d/m/Y', strtotime($student['date_affectation'])) 
                                                        : 'Non disponible' 
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-warning" 
                                                       data-bs-toggle="modal" data-bs-target="#changeEncadreurModal<?= $student['id'] ?>"
                                                       title="Changer d'encadreur">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal pour changer d'encadreur -->
                                            <div class="modal fade" id="changeEncadreurModal<?= $student['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                Changer l'encadreur de <?= htmlspecialchars($student['prenom'] . ' ' . $student['nom']) ?>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>
                                                                Encadreur actuel : 
                                                                <strong>
                                                                    <?= htmlspecialchars($student['encadreur_prenom'] . ' ' . $student['encadreur_nom']) ?>
                                                                </strong>
                                                            </p>
                                                            <form method="POST" action="index.php?controller=admin&action=changeEncadreur">
                                                                <input type="hidden" name="etudiant_id" value="<?= $student['id'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="nouveau_encadreur" class="form-label">Nouvel encadreur</label>
                                                                    <select class="form-select" name="nouveau_encadreur_id" id="nouveau_encadreur" required>
                                                                        <option value="">Sélectionner un encadreur</option>
                                                                        <?php 
                                                                        $db = Database::getConnection();
                                                                        $stmt = $db->query("SELECT * FROM encadreurs ORDER BY nom, prenom");
                                                                        $encadreurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                        
                                                                        foreach ($encadreurs as $encadreur):
                                                                            if ($encadreur['id'] != $student['encadreur_id']):
                                                                        ?>
                                                                            <option value="<?= $encadreur['id'] ?>">
                                                                                <?= htmlspecialchars($encadreur['prenom'] . ' ' . $encadreur['nom']) ?>
                                                                            </option>
                                                                        <?php 
                                                                            endif;
                                                                        endforeach;
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="d-grid">
                                                                    <button type="submit" class="btn btn-warning">
                                                                        <i class="fas fa-exchange-alt me-1"></i> Changer l'encadreur
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
                
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-filter me-2"></i> Filtres et statistiques
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total d'étudiants assignés</h5>
                                        <p class="display-5 text-success"><?= count($students) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Encadreur le plus actif</h5>
                                        <?php
                                        if (!empty($students)) {
                                            $encadreurs = [];
                                            foreach ($students as $student) {
                                                $encadreur = $student['encadreur_prenom'] . ' ' . $student['encadreur_nom'];
                                                if (!isset($encadreurs[$encadreur])) {
                                                    $encadreurs[$encadreur] = 0;
                                                }
                                                $encadreurs[$encadreur]++;
                                            }
                                            arsort($encadreurs);
                                            $encadreur_name = key($encadreurs);
                                            $count = current($encadreurs);
                                            echo "<p class='h5 text-primary'>$encadreur_name</p>";
                                            echo "<p class='h6'>$count étudiants</p>";
                                        } else {
                                            echo "<p class='text-muted'>Aucune donnée</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Affectations récentes</h5>
                                        <?php
                                        if (!empty($students)) {
                                            // Trier par date d'affectation décroissante
                                            usort($students, function($a, $b) {
                                                return strtotime($b['date_affectation']) - strtotime($a['date_affectation']);
                                            });
                                            // Prendre les 3 premiers
                                            $recent = array_slice($students, 0, 3);
                                            $recent_count = count($recent);
                                            echo "<p class='h5 text-info'>$recent_count récentes</p>";
                                            echo "<p class='small'>(dans les 7 derniers jours)</p>";
                                        } else {
                                            echo "<p class='text-muted'>Aucune donnée</p>";
                                        }
                                        ?>
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