<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Étudiant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if (isset($etudiant) && is_array($etudiant)): ?>
    <div class="container mt-5">
        <h2 class="mb-4">Mon Profil</h2>
        
        <form method="POST" action="index.php?controller=etudiant&action=updateProfile">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" id="nom" value="<?= htmlspecialchars($etudiant['nom'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" name="prenom" id="prenom" value="<?= htmlspecialchars($etudiant['prenom'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($etudiant['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <button type="submit" name="updateProfile" class="btn btn-primary">Mettre à jour le profil</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="alert alert-danger">Les informations de l'étudiant sont introuvables.</div>
<?php endif; ?>
</body>