<!-- WIDGET TOURNAMENT LATEST -->

<div class="card mt-3 last news">
    <h5 class="card-header"><i class="fas fa-newspaper"></i> Letzte Neuigkeiten</h5>
    <ul class="list-group list-group-flush active">
        {foreach item=$item from=$data}
            <li class="list-group-item">{$item.lastEdited|date_format:"d.m.Y"} // <a href="/news/details/{$item.newsId}">{$item.title}</a> <span class="badge badge-primary">{$item.categoryTitle}</span></li>
        {foreachelse}
            <li class="list-group-item">Aktuell gibt es keine News</li>
        {/foreach}
    </ul>
    <hr>
    <a href="/news" title="Alle Turniere"><i class="fas fa-list-ul"></i> alle Neuigkeiten</a>
</div>