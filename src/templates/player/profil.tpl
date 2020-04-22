{if !$player}
    <div class="alert alert-danger text-center">
        <p>Bitte wählen Sie einen gültigen User aus</p>
    </div>
{else}
    <div class="card card-profile text-center pt-2">
        <h4 class="card-title">{$player.firstName} {$player.lastName}</h4>
        <div class="card-block">
                <img src="/static/img/user/default_{if $player.gender == "Male"}m{else}w{/if}.png" name="about {$player.firstName} {$player.lastName}" width="140" height="140" border="0" class="card-img-profile"">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4 mb-4">
                <h5 class="card-header">Informationen</h5>
                <div class="card-body">
                    <p class="card-text">
                        <div class="row">
                            <div class="col-md-12">
                                {if $player.playerNr}
                                    <p><strong>Spielernummer:</strong> {$player.playerNr}</p>
                                {/if}

                                {if $player.clubName}
                                    <p><strong>Verein: </strong> {$player.clubName}</p>
                                {/if}
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <p class="text-right">
      <a class="btn btn-danger" href="javascript:history.back()">Zurück</a>
    </p>
{/if}
