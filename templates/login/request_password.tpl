<form action="" method="post">
    <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="request_password">
<div class="login-form">
    {include file="messages.tpl"}
    <h2>Passwort anfordern</h2>
    <div class="form-group">
      <input type="email" class="form-control login-field" value="" placeholder="Enter your Mail" id="loginFormLoginEmail" name="loginFormLoginEmail" required>
      <label class="login-field-icon fui-mail" for="{$variableNameEmail}"></label>
    </div>

    <input type="submit" name="request" class="btn btn-primary btn-block" value="Password anfordern">

    <a href="/pages/index.php" class="btn btn-warning btn-block mt-5" role="button">Zurück</a>
</div>
</form>
