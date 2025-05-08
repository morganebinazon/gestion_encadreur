<!-- students_without_encadreur.php -->
<div class="container mt-5">
  <h2>Étudiants sans Encadreur</h2>
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Nom</th>
        <th>Email</th>
        <th>Thème</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($students as $student): ?>
        <tr>
          <td><?= htmlspecialchars($student['nom']) ?></td>
          <td><?= htmlspecialchars($student['email']) ?></td>
          <td><?= htmlspecialchars($student['theme']) ?></td>
          <td>
            <a href="/admin/assign-encadreur?id=<?= $student['id'] ?>" class="btn btn-primary btn-sm">Affecter</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
