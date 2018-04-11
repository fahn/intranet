<h1 class="display-1 mb-5">Das Team</h1>
<div class="alert alert-info">
  <h2></h2>
  <p>Auf dieser Seite findet ihr alle Personen rund um den Vorstand, weitere Personen und die technischen Ansprechpartner. </p>
  <p>Diese Informationen findet ihr alternativ auf der <a href="http://www.bc-comet.de/training/vorstand/" target="_blank">BC Comet Webseite</a>.</p>
</div>
{foreach key=key item=list from=$row}
  {if $key == 1}
   <h2 class="display-3">Vorstand</h2>
  {else if $key == 2}
    <h3 class="display-3">Weitere Personen<h3>
  {else}
    <h3 class="display-3">Technische Ansprechpartner</h3>
  {/if}

  <div class="row mt-5 mb-5">
  {foreach item=user from=$list}
  <div class="col-md-3 mb-5 ">
  <div class="card">
    <img class="card-img-top" src="/static/img/user/{if $user.image}{$user.image}{else}default_{if $user.gender == "Male"}m{else}w{/if}.png{/if}" alt="{$user.name}">
    <div class="card-body">
      <h5 class="card-title"><a href="/pages/user.php?id={$user.userId}">{$user.name}</a></h5>
      <p class="card-text"><strong>{$user.position}</strong> {if $user.description}// {$user.description}{/if}</p>
    </div>
  </div>
  </div>
  {/foreach}
  </div>
{/foreach}