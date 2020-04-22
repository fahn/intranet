{include file="header.tpl" pageTitle=$pageTitle}
<body id="page">
  <div class="container">
    {include file="navi.tpl"}
    {include file="messages.tpl" pageTitle=$pageTitle}
    <div class="row">
      <div class="col-md-12">
        {if isset($content)}
          {$content}
        {/if}
      </div>
    </div>
<!-- closing  </div> in footer -->
{include file="footer.tpl"}
