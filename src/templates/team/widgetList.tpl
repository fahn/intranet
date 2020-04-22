<div class="card mt-4 mb-4">
    <h5 class="card-header">Das Team</h5>
    <div class="card-body">
        <p class="card-text">
            {foreach item=user from=$data}
              <a href="/pages/user.php?id={$user['userId']}">
                {$user['fullName']}
              </a><br>
            {foreachelse}
              Fehler. Bitte einen Admin kontaktieren
            {/foreach}
            <hr>
            Bei diesen Personen kannst du dich jederzeit melden.
        </p>
    </div>
</div>
