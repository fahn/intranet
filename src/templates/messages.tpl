{if isset($messages)}

    {foreach from=$messages item=message}
        <div class="alert alert-{$message.type}">
            {$message.message}
        </div>
    {/foreach}

{/if}