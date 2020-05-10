<form action="" method="post" >
  <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="Log In">
  <div class="login-form">
    {include file="messages.tpl"}

    <div class="input-group mb-2 mr-sm-2">
      <div class="input-group-prepend">
        <div class="input-group-text"><i class="fas fa-at"></i></div>
      </div>
       <input type="email" class="form-control login-field" value="" placeholder="E-Mail-Adresse" id="loginFormLoginEmail" name="loginFormLoginEmail" required autofocus>
    </div>

    <div class="input-group mb-2 mr-sm-2">
      <div class="input-group-prepend">
        <div class="input-group-text"><i class="fas fa-key"></i></div>
      </div>
      <input type="password" class="form-control login-field" value="" placeholder="Password" id="loginFormLoginPass" name="loginFormLoginPass" required>
    </div>

    <input type="submit" name="loginFormLoginAction" class="btn btn-success btn-block " value="Log In">

    <div class="row">
        <div class="col-md-{if isset($registerEnabled)}6{else}12{/if}">
            <a href="?action=request_password" class="btn btn-warning mt-5 btn-block" role="button">Passwort vergessen ?</a>
        </div>
        {if isset($registerEnabled)}
        <div class="col-md-6 text-right">
            <a href="?action=register" class="btn btn-outline-warning text-right mt-5" role="button" data-toggle="tooltip" data-placement="top" title="Funktioniert noch nicht! Bitte eine E-Mail an XXX senden">Registrieren</a>
        </div>
        {/if}
    </div>
    <hr>
    <p class="text-center">
      {if isset($imprint)}
          <a href="{$imprint}">Impressum</a>
      {/if}
      {if isset($imprint) && isset($disclaimer)}
        //
      {/if}
      {if isset($disclaimer)}
          <a href="{$disclaimer}">Datenschutz</a>
      {/if}
    </p>
    <p class="mb-3 text-muted text-center">© 2017-{$smarty.now|date_format:"%Y"}</p>

    </div>
  </div>
</form>
