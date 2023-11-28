<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"
<i class="far fa-bell"></i><span class="badge badge-danger">11</span>
</a>
<ul class="dropdown-menu notify-drop">
    <div class="notify-drop-title">
        <div class="row">
            <div class="col-md-12">Benachrichtung</div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right"><a href="" class="rIcon allRead" data-tooltip="tooltip" data-placement="bottom" title="tümü okundu."><i class="fa fa-dot-circle-o"></i></a></div>
        </div>
    </div>
    <!-- end notify title -->
    <!-- notify content -->
    <div class="drop-content">
        {foreach item=$item from=$notification}
            <li>{$item.text}</li>
        {/foreach}
    </div>
</ul>


<button type="button" class="btn btn-primary">
  Notifications <span class="badge badge-light">4</span>
</button>
