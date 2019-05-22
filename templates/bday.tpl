<div class="card mb-4">
    <h5 class="card-header"><i class="fas fa-birthday-cake"></i> Geburtstagskalender</h5>
    <div class="card-body">
        {foreach item=$user from=$bdays}
            <p class="card-text text-center">
                <a href="{$user.linkToUser}">{$user.userName}</a> ({$user.years} Jahre - {$user.bday|date_format:"%d.%m.%Y"})
            </p>
        {/foreach}
    </div>
</div>
