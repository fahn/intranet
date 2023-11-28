<div class="card mb-4">
    <h5 class="card-header"><i class="fas fa-gamepad"></i> Deine letzten 5 Spiele</h5>
    <div class="card-body">
        <div class="card-text">
            <table class="table table-striped table-hover">
                {foreach item=game from=$data}
                <tr>
                    <td>{$game.time|date_format:"d.m.Y"}</td>
                    <td>{$game.name}</td>
                    <td>{$game.chicken}</td>
                </tr>
                <tr>
                    <td class="text-center small " colspan="3">Score: {$game.sets}</td>
                </tr>
                {foreachelse}
                Du hast noch keine Spiele gemacht.
                {/foreach}
            </table>
            <hr>
            {if isset($link)}
                <a href="{$link}" alt="Komplette Rangliste" title="Komplette Rangliste"><i class="fas fa-list-ol"></i> komplette Rangliste</a>
            {/if}
        </div>
    </div>
</div>
