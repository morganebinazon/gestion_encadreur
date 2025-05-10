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
    <title><?= isset($encadreur) ? "Modifier un encadreur" : "Ajouter un encadreur" ?> - Administration</title>
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
                        <i class="fas fa-<?= isset($encadreur) ? "edit" : "plus" ?> me-2"></i>
                        <?= isset($encadreur) ? "Modifier un encadreur" : "Ajouter un encadreur" ?>
                    </h2>
                    <a href="index.php?controller=admin&action=encadreurs" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="index.php?controller=admin&action=<?= isset($encadreur) ? 'updateEncadreur' : 'addEncadreur' ?>">
                            <?php if (isset($encadreur)): ?>
                                <input type="hidden" name="id" value="<?= $encadreur['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="nom" id="nom" 
                                           value="<?= htmlspecialchars($encadreur['nom'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="prenom" id="prenom" 
                                           value="<?= htmlspecialchars($encadreur['prenom'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email (optionnel)</label>
                                <input type="email" class="form-control" name="email" id="email" 
                                       value="<?= htmlspecialchars($encadreur['email'] ?? '') ?>">
                                <div class="form-text">
                                    Si un email est fourni, un compte sera créé automatiquement pour l'encadreur.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Compétences</label>
                                <div class="border rounded p-3">
                                    <?php 
                                    $selected = isset($encadreur) ? explode(',', $encadreur['competences']) : [];
                                    $allSkills = ['AL', 'SI', 'SRC', 'RES', 'DATA', 'WEB', 'MOBILE', 'IOT', 'IA'];
                                    foreach ($allSkills as $skill): 
                                    ?>
                                        <div class="form-check form-check-inline mb-2">
                                            <input class="form-check-input" type="checkbox" name="competences[]" 
                                                   value="<?= $skill ?>" id="skill-<?= $skill ?>" 
                                                   <?= in_array($skill, $selected) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="skill-<?= $skill ?>"><?= $skill ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-1"></i>
                                    <?= isset($encadreur) ? 'Mettre à jour' : 'Ajouter' ?>
                                </button>
                                <a href="index.php?controller=admin&action=encadreurs" class="btn btn-secondary btn-lg">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>