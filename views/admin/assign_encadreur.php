<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Affectation d'un étudiant></title>
</head>
<body>
<div class="container mt-5">
  <h2>Affecter un étudiant à l'encadreur : <?= htmlspecialchars($encadreur['nom']) ?> <?= htmlspecialchars($encadreur['prenom']) ?></h2>

  <form method="POST" action="index.php?controller=admin&action=saveAffectation">
    <input type="hidden" name="encadreur_id" value="<?= $encadreur['id'] ?>">

    <div class="mb-3">
      <label for="etudiant_id" class="form-label">Choisir un étudiant</label>
      <select name="etudiant_id" id="etudiant_id" class="form-select" required>
        <option value="">Sélectionner un étudiant</option>
        <?php foreach ($etudiants as $etudiant): ?>
          <option value="<?= $etudiant['id'] ?>">
            <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Affecter</button>
  </form>
</div>
</body>
</html>