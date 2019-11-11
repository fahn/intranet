<h2 class="display-1 mb-5">Profil aktualisieren</h2>

<ul class="nav nav-tabs justify-content-center" style="margin-bottom: 20px;">
  <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#profil">Profil</a>
  </li>
  <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#image">Bild</a>
  </li>
  <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#password">Password</a>
  </li>
</ul>

<div class="tab-content">
    <div id="profil" class="tab-pane container active">
        {include file="user/myInformation.tpl" vars=$vars}
    </div>
    
    <div id="image" class="tab-pane container">
        {include file="user/myImage.tpl" vars=$vars}
    </div>
    
    <div id="password" class="tab-pane container">
        {include file="user/myPassword.tpl" vars=$vars}
    </div>
</div>
