<h1>Kategorien</h1>
<div class="alert alert-warning fade in alert-dismissible show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true" style="font-size:20px">×</span>
    </button> 
    <strong>Info!</strong> Diese Kategorien gelten für den FAQ- und News-Bereich.
</div>

<table class="table">
    <thead>
        <tr>
            <th scope="col">Titel</th>
            <th scope="col">Option</th>
        </tr>
    </thead>
    <tbody>
        {foreach item=cat from=$FaqCategory}
        <tr>
            <th scope="row">{$cat.title}</th>
            <td><a href="#">editieren</a> <a href="#">Löschen</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>
