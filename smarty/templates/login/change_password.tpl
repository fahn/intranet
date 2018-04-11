<form action="" method="post">
    <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="change_password">
    <input type="hidden" name="loginFormLoginToken"  id="loginFormLoginToken"  value="{$token}">
    <input type="hidden" name="loginFormLoginEmail"  id="loginFormLoginEmail"  value="{$mail}">
    <div class="login-form">
        {include file="messages.tpl"}

        <h2>Passwort ändern</h2>

        <div class="form-group">
          <input type="password" class="form-control login-field" value="" placeholder="Enter password" id="loginFormLoginPass" name="loginFormLoginPass" required>
          <label class="login-field-icon fui-lock" for="loginFormLoginPass"></label>
        </div>

        <div class="form-group">
          <input type="password" class="form-control login-field" value="" placeholder="repeat password" id="loginFormLoginPass2" name="loginFormLoginPass2" required>
          <label class="login-field-icon fui-lock" for="loginFormLoginPass2"></label>
        </div>

        <div class="row">
          <div class="col-md-6">
            <input type="submit" name="request" class="btn btn-primary" value="Password ändern">
          </div>

          <div class="col-md-6 text-right">
            <a href="https://int.bc-comet.de/pages/" class="btn btn-warning" role="button">Abbruch</a>
          </div>
        </div>
    </div>
</form>
