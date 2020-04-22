<form action="" method="post">
    <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="request_password">
    <div class="login-form">
      {include file="messages.tpl"}
    <h2 class="display-4 mb-3 mt-3 mr-sm-3">Passwort anfordern</h2>
    <label class="sr-only" for="{$variableNameEmail}"></label>
    <div class="input-group mb-2 mr-sm-2">
      <div class="input-group-prepend">
        <div class="input-group-text"><i class="fas fa-at"></i></div>
      </div>
      <input type="email" class="form-control login-field" value="" placeholder="E-Mail-Adresse" id="loginFormLoginEmail" name="loginFormLoginEmail" required>
    </div>
  </div>

  <input type="submit" name="request" class="btn btn-primary btn-block" value="Password anfordern">
  <a href="/pages/index.php" class="btn btn-warning btn-block mt-5" role="button">Zurück</a>
  </div>
</form>
