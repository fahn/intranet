{include file="header.tpl"}

<div id="formUserLogin" class="login-screen">
    <div class="login-icon">
        <img src="../design/img/badminton.png" alt="BC Comet INTERN">
        <h4>BC Comet <small>Intern</small></h4>
    </div>
    <form action="{$formTO}" method="post">
        <div class="login-form">
          {include file="messages.tpl"}
          <div class="alert alert-info">
            Wenn du Zugang zu diesem System willst, dann melde dich bei Stefan Metzner beim Training.</p>
          </div>
          <div class="form-group">
              <input type="email" class="form-control login-field" value="" placeholder="Enter your Mail" id="{$variableNameEmail}" name="{$variableNameEmail}" required>
              <label class="login-field-icon fui-user" for="{$variableNameEmail}"></label>
          </div>

          <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="Password" id="{$variableNamePassw}" name="{$variableNamePassw}" required>
              <label class="login-field-icon fui-lock" for="{$variableNamePassw}"></label>
          </div>

          <input type="submit" name="{$variableNameAction}" class="btn btn-primary btn-lg btn-block" value="{$variableNameActionLogin}">

          <!-- <a class="login-link" href="#">Passwort vergessen?</a>-->
        </div>
    </form>
</div>

{include file="footer.tpl"}
