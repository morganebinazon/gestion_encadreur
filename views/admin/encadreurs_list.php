<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Liste des Encadreurs</title>
</head>
<body>
    
</body>
</html>
<div class="container mt-5">
  <h2 class="mb-4">Liste des Encadreurs</h2>
  <a href="http://localhost/gestion_encadreur/index.php?controller=admin&action=addEncadreur" class="btn btn-primary mb-3">Ajouter un encadreur</a>
  <a href="index.php?controller=admin&action=assignedStudents" class="btn btn-secondary mb-3">Étudiants assignés</a>
<a href="index.php?controller=admin&action=unassignedStudents" class="btn btn-outline-secondary mb-3">Étudiants non assignés</a>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Nom</th>
        <th>Prenom</th>
        <th>Compétences</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($encadreurs as $encadreur): ?>
        <tr>
          <td><?= htmlspecialchars($encadreur['nom']) ?></td>
          <td><?= htmlspecialchars($encadreur['prenom']) ?></td>
          <td><?= htmlspecialchars($encadreur['competences']) ?></td>
          <td>
            <a href="index.php?controller=admin&action=editEncadreur&id=<?= $encadreur['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
            <a href="index.php?controller=admin&action=deleteEncadreur&id=<?= $encadreur['id'] ?>" onclick="return confirm('Confirmer la suppression ?');" class="btn btn-danger btn-sm">Supprimer</a>
            <!-- Nouveau bouton pour assigner un étudiant -->
  <a href="index.php?controller=admin&action=assignForm&id=<?= $encadreur['id'] ?>" class="btn btn-info btn-sm">Assigner un étudiant</a>
</td>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
