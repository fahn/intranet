<form action="{$formTO}" method="post">
  <input type="hidden" name="loginFormLoginAction" id="loginFormLoginAction" value="Log In">
  <div class="login-form">
    {include file="messages.tpl"}

    <div class="alert alert-info">
     <p class="text-center"> <strong>~~ {$pageTitle} ~~</strong> </p>
       <p> Möchtest du Zugang zu unserem neuen System haben, dann schicke eine E-Mail an: <a href="mailto:stefan@weinekind.de?subject=Zugang {$pageTitle}&body=Hallo Stefan,%0D%0A%0D%0Aich hätte gerne Zugang zum BC Comet Intranet:%0D%0A%0D%0AMein Vorname:%0D%0AMein Nachname:%0D%0A">Stefan Metzner</a>.<br> Weitere Informationen folgen dann via E-Mail.</p>
    </div>
    <div class="form-group">
        <input type="email" class="form-control login-field" value="" placeholder="E-Mail-Adresse" id="{$variableNameEmail}" name="{$variableNameEmail}" required>
    </div>

    <div class="form-group">
        <input type="password" class="form-control login-field" value="" placeholder="Password" id="{$variableNamePassw}" name="{$variableNamePassw}" required>
    </div>

    <input type="submit" name="{$variableNameAction}" class="btn btn-success btn-block " value="{$variableNameActionLogin}">

    <a href="?action=request_password" class="btn btn-warning btn-block mt-5" role="button">Passwort vergessen ?</a>
    <hr>
    <p class="text-center">
      {if $imprint}
          <a href="{$imprint}">Impressum</a> //
      {/if}
      {if $disclaimer}
          <a href="{$disclaimer}">Datenschutz</a>
      {/if}
    </p>

    </div>
  </div>
</form>
