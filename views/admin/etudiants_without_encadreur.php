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
    <title>Étudiants Sans Encadreur - Administration</title>
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
                    <h2><i class="fas fa-user-times me-2"></i> Étudiants Sans Encadreur</h2>
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
                        <?php if (empty($etudiants)): ?>
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
                                        <?php foreach ($etudiants as $etudiant): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                                                <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                                                <td><?= htmlspecialchars($etudiant['email'] ?? 'Non défini') ?></td>
                                                <td><?= htmlspecialchars($etudiant['classe'] ?? 'Non définie') ?></td>
                                                <td>
                                                    <?= isset($etudiant['created_at']) 
                                                        ? date('d/m/Y', strtotime($etudiant['created_at'])) 
                                                        : 'Non disponible' 
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="#" class="btn btn-sm btn-success" 
                                                           data-bs-toggle="modal" data-bs-target="#assignModal<?= $etudiant['id'] ?>"
                                                           title="Assigner un encadreur">
                                                            <i class="fas fa-user-plus"></i> Assigner
                                                        </a>
                                                    </div>
                                                    
                                                    <!-- Modal pour assigner un encadreur -->
                                                    <div class="modal fade" id="assignModal<?= $etudiant['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">
                                                                        Assigner un encadreur à <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="index.php?controller=admin&action=saveAffectation">
                                                                        <input type="hidden" name="etudiant_id" value="<?= $etudiant['id'] ?>">
                                                                        <div class="mb-3">
                                                                            <label for="encadreur_id_<?= $etudiant['id'] ?>" class="form-label">Sélectionner un encadreur</label>
                                                                            <select class="form-select" name="encadreur_id" id="encadreur_id_<?= $etudiant['id'] ?>" required>
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
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($etudiants)): ?>
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle me-2"></i> Informations importantes
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p>
                                    <i class="fas fa-info-circle me-2"></i>
                                    Il y a actuellement <strong><?= count($etudiants) ?> étudiants</strong> sans encadreur.
                                    Assurez-vous de leur assigner un encadreur avant la date limite de soumission des projets.
                                </p>
                                <p>
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Date limite d'assignation : <strong>15 juin 2025</strong>
                                </p>
                                <p>
                                    <i class="fas fa-bell me-2"></i>
                                    Les étudiants seront notifiés par email dès qu'un encadreur leur sera assigné.
                                </p>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <a href="index.php?controller=admin&action=autoAssign" 
                                       class="btn btn-warning mb-2"
                                       onclick="return confirm('Êtes-vous sûr de vouloir lancer l\'affectation automatique ? Cela va tenter d\'assigner tous les étudiants à des encadreurs en fonction de leurs compétences.');">
                                        <i class="fas fa-magic me-1"></i> Lancer l'affectation automatique
                                    </a>
                                    <a href="index.php?controller=admin&action=notifyUnassigned" 
                                       class="btn btn-outline-secondary"
                                       onclick="return confirm('Voulez-vous envoyer un message à tous les étudiants sans encadreur ?');">
                                        <i class="fas fa-envelope me-1"></i> Notifier les étudiants
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>