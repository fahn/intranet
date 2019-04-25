<div class="alert alert-warning">
    <p class="text-center">Alte Bilder werden nicht gelöscht, sondern Serverseitig gespeichert.</p>
</div>
<div id="formUserRegister">
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="userRegisterFormAction" id="userRegisterFormAction" value="changeImage">

    <h2 class="mt-5 mb-2">Profilbild</h2>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for = "userRegisterAccountImage">Bild (Nur jpg/png-Bilder mit einer max. Größe von 2MB sind erlaubt):</label>
          <input class="form-control-file border" type="file" id="userRegisterAccountImage" name="userRegisterAccountImage[]" placeholder="" value="">
        </div>
      </div>
    </div>
    <input class="btn btn-success" type="submit" name="submit" value="Bild hochladen">
  </form>
</div>
