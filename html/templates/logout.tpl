{config_load file="test.conf" section="setup"}
{include file="header.tpl" title=foo}

<div class="container">

    {include file="navi.tpl"}
    {include file="messages.tpl"}

    <div id = "formUserLogout" class = "small">
        <form>
            <p>Hello {$currentUserName}! You are logged in!</p>
            <input
                type		= "submit"
                name		= "{$variableNameAction}"
                value		= "{$variableNameActionLogout}"
                formaction	= "{$formAction}"
                formmethod	= "post"
            />
        </form>
    </div>

        
{include file="footer.tpl"}