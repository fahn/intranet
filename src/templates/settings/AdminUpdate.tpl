<h1 class="display-3 mb-5">Einstellung {if $action == 'edit'}editieren{else}hinzufügen{/if}</h1>
<form action="" method="post">
    <input type="hidden" name="settingFormAction" value="{if $action == 'edit'}Update{else}Insert{/if}">
    {if $action == 'edit'}
        <input type="hidden" name="settingsettingId" value="{$item.settingId}">
    {/if}

    <div class="form-group">
        <label for="settingName">Name</label>
        <input type="text" class="form-control" id="settingName" name="settingName" value="{$item.name}" readonly>
        <small id="emailHelp" class="form-text text-muted">Bitte nicht ändern</small>
    </div>

    <div class="form-group">
        <label for="settingDataType">Datentyp</label>
        <select class="form-control" id="settingDataType" name="settingDataType">
            {html_options options=$dataTypeOptions selected=$item.dataType}
        </select>
    </div>

    {if $item.dataType == "string"}
        <div class="form-group">
            <label for="settingValue">Wert</label>
            <input type="text" class="form-control" id="settingValue" name="settingValue" value="{$item.value}">
        </div>
    {else if $item.dataTyp == "array"}
        <div class="form-group">
            <label for="settingValue">Wert</label>
            <textarea class="form-control" id="settingValue" name="settingValue">{$item.value}</textarea>
        </div>
    {else}
        <div class="form-group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="settingValue" id="settingValue1" value="true" {if $item.value}checked="checked"{/if}>
                <label class="form-check-label" for="settingValue1">True</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="settingValue" id="settingValue2" value="false" {if !$item.value}checked="checked"{/if}>
                <label class="form-check-label" for="settingValue2">False</label>
            </div>
        </div>
    {/if}


    <button type="submit" class="btn btn-primary">{if $action == 'edit'}Editieren{else}Submit{/if}</button>

    <a href="{$links.home}" class="btn btn-danger">Zurück</a>

</form>
