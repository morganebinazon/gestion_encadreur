<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Listes des étudiants non assignés</title>
</head>
<body>  
</body>
</html>
<div class="container mt-5">
  <h2 class="mb-4">Étudiants non assignés</h2>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Classe</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $student): ?>
        <tr>
          <td><?= htmlspecialchars($student['nom']) ?></td>
          <td><?= htmlspecialchars($student['prenom']) ?></td>
          <td><?= htmlspecialchars($student['email']) ?></td>
          <td><?= htmlspecialchars($student['classe']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
