<h3>Support</h3>
<div class="alert alert-info">
    <p>Wenn du Fragen, Informationen, Anregungen oder dich am Portal beteiligen willst, dann schreibe uns:</p>
    <p>Deine Nachricht wird als E-Mail an uns geschickt</p>
</div>
<form action="" method="post">
  <input type="hidden" name="supportFormAction" id="supportFormAction" value="Contact Us">

  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="supportSubject">Dein Betreff:</label>
        <input class="form-control"  type="text" id="supportSubject" name="supportSubject" placeholder="Dein Betreff" required>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
      <label for="sel1">Kategorie:</label>
      <select class="form-control" id="sel1">
        <option>Allgemeines</option>
        <option>Probleme beim Intern</option>
        <option>Vorstand</option>
        <option>Sosntiges</option>
      </select>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="supportMessage">Deine Nachricht:</label>
        <textarea class="form-control" rows="10"  type="text" id="supportMessage" name="supportMessage" placeholder="Deine Nachricht" required>{$text}</textarea>
      </div>
    </div>
  </div>

  <input class="btn btn-success" type="submit" name="submit" value="Nachricht senden">
</form>
