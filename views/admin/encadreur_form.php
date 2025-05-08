<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Ajouter un encadreur</title>
</head>
<body>   
</body>
</html>
<div class="container mt-5">
  <h2><?= isset($encadreur) ? "Modifier" : "Ajouter" ?> un Encadreur</h2>
  <form method="POST" action="index.php?controller=admin&action=<?= isset($encadreur) ? 'updateEncadreur' : 'addEncadreur' ?>">
    <input type="hidden" name="id" value="<?= $encadreur['id'] ?? '' ?>">

    <div class="mb-3">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" class="form-control" name="nom" value="<?= $encadreur['nom'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
      <label for="prenom" class="form-label">Prénom</label>
      <input type="text" class="form-control" name="prenom" value="<?= $encadreur['prenom'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Compétences</label><br>
      <?php 
        $selected = isset($encadreur) ? explode(',', $encadreur['competences']) : [];
        $allSkills = ['AL', 'SI', 'SRC'];
        foreach ($allSkills as $skill): 
      ?>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="competences[]" value="<?= $skill ?>" 
            <?= in_array($skill, $selected) ? 'checked' : '' ?>>
          <label class="form-check-label"><?= $skill ?></label>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="submit" class="btn btn-success"><?= isset($encadreur) ? 'Mettre à jour' : 'Ajouter' ?></button>
  </form>
</div>
