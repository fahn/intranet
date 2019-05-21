<form action="{$formTO}" method="post" class="form-signin">
  <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="Log In">
  <div class="login-form">
    {include file="messages.tpl"}

    <div class="form-group">
        <input type="email" class="form-control login-field" value="" placeholder="E-Mail-Adresse" id="{$variableNameEmail}" name="{$variableNameEmail}" required autofocus>
    </div>

    <div class="form-group">
        <input type="password" class="form-control login-field" value="" placeholder="Password" id="{$variableNamePassw}" name="{$variableNamePassw}" required>
    </div>

    <input type="submit" name="{$variableNameAction}" class="btn btn-success btn-block " value="{$variableNameActionLogin}">

    <div class="row">
        <div class="col-md-{if $registerEnabled}6{else}12{/if}">
            <a href="?action=request_password" class="btn btn-warning mt-5 btn-block" role="button">Passwort vergessen ?</a>
        </div>
        {if $registerEnabled}
        <div class="col-md-6 text-right">
            <a href="?action=register" class="btn btn-outline-warning text-right mt-5" role="button" data-toggle="tooltip" data-placement="top" title="Funktioniert noch nicht! Bitte eine E-Mail an XXX senden">Registrieren</a>
        </div>
        {/if}
    </div>
    <hr>
    <p class="text-center">
      {if $imprint}
          <a href="{$imprint}">Impressum</a>
      {/if}
      {if $imprint && $disclaimer}
        //
      {/if}
      {if $disclaimer}
          <a href="{$disclaimer}">Datenschutz</a>
      {/if}
    </p>
    <p class="mb-3 text-muted text-center">© 2017-{$smarty.now|date_format:"%Y"}</p>

    </div>
  </div>
</form>
