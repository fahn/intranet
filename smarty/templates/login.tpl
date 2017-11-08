{include file="header.tpl"}

{include file="navi.tpl"}

{include file="messages.tpl"}




<div id="formUserLogin" class="login-screen">
    <div class="login-icon">
        <img src="../design/img/badminton.png" alt="Welcome to Mail App">
        <h4>BC Comet <small>Rangliste</small></h4>
    </div>
    <form>
        <div class="login-form">
            <div class="form-group">
                <input type="text" class="form-control login-field" value="" placeholder="Enter your Mail" id="{$variableNameEmail}" name="{$variableNameEmail}">
                <label class="login-field-icon fui-user" for="{$variableNameEmail}"></label>
            </div>

            <div class="form-group">
                <input type="password" class="form-control login-field" value="" placeholder="Password" id="{$variableNamePassw}" name="{$variableNamePassw}">
                <label class="login-field-icon fui-lock" for="{$variableNamePassw}"></label>
            </div>
        
            <input type="submit" name="{$variableNameAction}" class="btn btn-primary btn-lg btn-block" value="{$variableNameActionLogin}" formaction="{$formTO}" formmethod="post">
            <a class="login-link" href="#">Lost your password?</a>
        </div>
    </form> 
</div>


        
{include file="footer.tpl"}