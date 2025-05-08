<div class="container mt-5">
  <h2>Étudiants sans encadreur</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($etudiants as $etudiant): ?>
        <tr>
          <td><?= htmlspecialchars($etudiant['nom']) ?></td>
          <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
          <td><?= htmlspecialchars($etudiant['email']) ?></td>
          <td>
            <a href="/gestion_encadreur/index.php?controller=admin&action=assignForm&id=<?= $etudiant['id'] ?>" class="btn btn-primary btn-sm">Assigner</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
