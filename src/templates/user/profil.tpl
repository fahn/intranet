<!-- ADMIN TOURNAMENT LIST -->

{include file="page_wrap_header.tpl"}

{if !isset($user)}
    <div class="alert alert-danger text-center">Bitte wählen Sie einen gültigen User aus</div>
{else}

    {if $user.userId == $userId || $isAdmin}
        <p class="text-right mt-5 mb-5">
            <a class="btn btn-danger" href="/pages/myAccount.php">Editieren</a>
            {if $isAdmin}
                <a class="btn btn-danger" href="/pages/adminAllUser.php?action=edit&id={$user.userId}">Admin-Edit</a>
            {/if}
        </p>
    {/if}

    <div class="card card-profile text-center pt-2">
        <h4 class="card-title">{$user.firstName} {$user.lastName}</h4>
        <div class="card-block">
            {if $user.image}
                <img src="/static/img/user/{$user.image}" name="aboutme" width="140" height="140" border="0" class="card-img-profile"">
            {else}
                <img src="/static/img/user/default_{if $user.gender == "Male"}m{else}w{/if}.png" name="about {$user.firstName} {$user.lastName}" width="140" height="140" border="0" class="card-img-profile"">
            {/if}
        </div>
    </div>



    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4 mb-4">
                <h5 class="card-header">Informationen</h5>
                <div class="card-body">
                    <p class="card-text">
                        <div class="row">
                            <div class="col-md-6">
                                {if isset($isAdmin) || isset($isReported)}
                                  {if $user.bday|intval != 0}
                                   <p><strong>Geburtstag:</strong> {$user.bday|date_format:"d.m.Y"}</p>
                                  {/if}
                                  {if isset($user.email)}
                                    <p><strong>E-Mail:</strong> {$user.email}</p>
                                  {/if}
                                  {if isset($user.phone)}
                                    <p><strong>Telefon:</strong> {$user.phone}</p>
                                  {/if}
                                {/if}
                            </div> 
                            <div class="col-md-6"> 
                                {if isset($user.playerId)} 
                                    <p><strong>Spielernummer:</strong> {$user.playerId}</p>
                                {/if}
    
                                {if isset($club.name)}
                                    <p><strong>Verein: </strong> {$club.name}</p>
                                {/if}
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        {if isset($latestGamesInRanking)}
        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Letzten Spiele im Ranking</h5>
                <div class="card-body">
                    {$latestGamesInRanking}
                </div>
            </div>
        </div>
        {/if}
        
        {if isset($latestTournament)}
        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Letzten 10 offizielle Turniere/Ranglisten</h5>
                <div class="card-body">
                    {$latestTournament}
                </div>
            </div>
        </div>
        {/if}
    </div>
    
    <p class="text-right">
      <a class="btn btn-danger" href="javascript:history.back()">Zurück</a>
    </p>
{/if}



{include file="page_wrap_footer.tpl"}