

<?php require_once __DIR__ .'/../includes/header.php' ?>
    <div class="create-group-page">
      <h1 class="page-title">Skapa ny grupp</h1>
      <form class="form" action="../actions/store_group.php" method="POST">
        <div class="form-group">
          <label class="form-label" for="name" >Gruppnamn</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="description">Beskrivning</label>
          <textarea  id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Skapa grupp</button>
        
     </form>
    
    </div>
<?php require_once __DIR__ .'/../includes/footer.php' ?>  
