{include file="header.tpl"}

<div class="container">

    {include file="navi.tpl"}
    {include file="messages.tpl"}



    <div class="row">
      <div class="col-md-12">
        {if isset($content)}
          {$content}
        {/if}
      </div>
    </div>



{include file="footer.tpl"}
