{include file="header.tpl"}

<body id="login">

<!--
    you can substitue the span of reauth email for a input with the email and
    include the remember me checkbox
    -->
    <div class="container">
        <div class="card card-container">
            <!-- <div class="alert alert-danger">
              Wartungsarbeiten! Es kann zu Störungen kommen!
            </div>
          -->
            <img id="profile-img" class="profile-img-card" src="/static/img/logo.png" />
            <p id="profile-name" class="profile-name-card"></p>
            {$content}

        </div><!-- /card-container -->
    </div><!-- /container -->

{include file="footer.tpl"}
