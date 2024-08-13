 <!-- ADMIN LOGS LIST -->

{include file="page_wrap_header.tpl"}

<h1 class="display-3 mb-5">Logs</h1>

<div class="table-responsive">
    <table class="table table-striped table-hover" data-toggle="table" data-pagination="true" data-search="true" data-page-list="50">
        <thead>
            <tr>
                <th scope="col" data-sortable="true" data-field="date">Datum</th>
                <th scope="col" data-sortable="true" data-field="user">User</th>
                <th scope="col">Action / Tabelle</th>
                <th scope="col">Details</th>
                <th scope="col">Log-Data</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=item from=$logList}
            <tr>
                <td scope="row">{$item.tstamp|date_format:"d.m.Y H:i"}</td>
                <td scope="row">{$item.userId}</td>
                <td scope="row">{$item.action} / {$item.fromTable}</td>
                <td scope="row">{$item.details}</td>
                <td scope="row">{$item.logdata}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{include file="page_wrap_footer.tpl"}