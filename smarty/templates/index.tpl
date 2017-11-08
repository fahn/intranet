{include file="header.tpl"}

<div class="container">

    {include file="navi.tpl"}
    {include file="messages.tpl"}



    <div id="row">
        {if isset($content)}
            {$content}
        {/if}
    </div>


        
{include file="footer.tpl"}