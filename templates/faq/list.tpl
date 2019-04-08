<h1>FAQ</h1>

<div id="accordion_search_bar_container">
    <input type="search" id="accordion_search_bar" placeholder="Search"/>
</div>

<div class="container">
{if $isAdmin OR $isReporter}
    <div class="row">
        <div class="col-lg-12">
            <h3>Admin</h3>
            <a class="btn btn-primary" href="{$links.category}" role="button">Kategorien</a>
            <a class="btn btn-primary" href="{$links.list}" role="button">List FAQ</a>
        </div>
    </div>
{/if}

<div class="panel-group" id="accordion" role="tablist"  aria-multiselectable="true">
    <h2>{$category.title}</h2>
    {foreach item=category from=$FaqGroupedByCategory}
        {foreach key=itemKey item=item from=$category.rows}
            <div class="panel panel-success"  id="collapseOne_container">
                <div class="panel-heading" role="tab" id="heading{$itemKey}">
                    <h4 class="panel-title"><a role="button"  data-toggle="collapse"  data-parent="#accordion"  href="#collapse{$itemKey}"  aria-expanded="true" aria-controls="collapse{$itemKey}">{$item.title}</a></h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        {$item.text}
                        <hr>
                        Erstellt: {$item.createdBy}<br>
                        Bearbeitet: {$item.lastEdited}<br>
                    </div>
                </div>
            </div>
        {/foreach}
    {/foreach}
</div>


<hr>
{literal}
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        event.preventDefault();
        /* Get input value on change */
        //$inputs = $(this);

        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        $(this).parent(".result").empty();

        //$inputs.prop("disabled", true);
        if(inputVal.length){
            request = $.ajax({
              type: "POST",
              dataType: 'text',
              contentType: 'application/x-www-form-urlencoded',
              url: "/ajax/player.php",
              data: $(this).serialize()
            });

            request.done(function( data ) {
                console.log(data);
              resultDropdown.html(data);
            });

            request.always(function () {
                // Reenable the inputs
                //$inputs.prop("disabled", false);
            });
        } else{
            resultDropdown.empty();
        }
    });

    // Set search input value on click of result item
    $(document).on("click", ".result p", function(){
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result").empty();
    });
});
</script>
{/literal}

<div class="search-box">
    <input type="text" autocomplete="off" id="playerSearch" name="playerSearch" placeholder="Search country..." />
    <div class="result"></div>
</div>
