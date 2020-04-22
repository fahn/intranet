{if isset($pagination)}
  <nav aria-label="Page navigation example" class="mt-1 mb-1">
    <ul class="pagination justify-content-center">
      {if isset($smarty.get.page) && $smarty.get.page > 1}
      <li class="page-item">
            <a class="page-link" href="?page={$smarty.get.page-1}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
      {/if}
      <!-- Make dropdown appear above pagination -->
      {foreach item=pn from=$pagination}
          {assign var="max" value=$pn.id}
            <li class="page-item {$pn.status}"><a class="page-link" href="?page={$pn.id}">{$pn.id}</a></li>
      {/foreach}

      {if !isset($smarty.get.page)}
        {assign "min"  "2"}
      {else}
        {assign "min"  $smarty.get.page}
      {/if}

      {if $max > 1 && $min < $max}
        <li class="page-item">
            <a class="page-link" href="?page={$smarty.get.page+1}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
        {/if}
    </ul>
  </nav>
{/if}
