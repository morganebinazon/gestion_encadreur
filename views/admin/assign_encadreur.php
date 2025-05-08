<!-- assign_encadreur.php -->
<div class="container mt-5">
  <h2>Affecter un Encadreur à <?= $student['nom'] ?> <?= $student['prenom'] ?></h2>

  <form method="POST" action="/admin/assign-encadreur">
    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

    <div class="mb-3">
      <label class="form-label">Choisir un encadreur compatible</label>
      <select name="encadreur_id" class="form-select" required>
        <?php foreach ($encadreurs as $encadreur): ?>
          <option value="<?= $encadreur['id'] ?>">
            <?= $encadreur['nom'] ?> <?= $encadreur['prenom'] ?> (<?= $encadreur['competences'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Affecter</button>
  </form>
</div>
