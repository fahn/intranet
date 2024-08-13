      </div>
    </div>
    <div class="mt-5" style="display: block">&nbsp;</div>
  </div>
  {if !isset($badtra.copyright) || $badtra.copyright == true}
  <footer>
     <nav class="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
          <a class="navbar-brand" href="{$badtra.url}">Badtra</a>
          <div class="collapse navbar-collapse" id="navbarCollapse">
              <ul class="navbar-nav mr-auto">
                {if isset($links.docs)}
                      <li class="nav-item"><a class="nav-link" href="https://docs.badtra.de">Manual</a></li>
                {/if}
                  <li class="nav-item"><a class="nav-link disabled" href="#">Version: {$version|default:"1.0.7"}</a></li>
              </ul>
              </div>

      </nav>
  </footer>
  {/if}

    


    <!-- datepicker -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="//cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="//npmcdn.com/flatpickr@4.5.7/dist/l10n/de.js"></script>

    <!-- font awesome -->
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <!-- select 2 -->
    <link href="//cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/i18n/de.js"></script>

    <!-- sortable tables -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.js"></script>
    <!-- Latest compiled and minified Locales -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/locale/bootstrap-table-de-DE.min.js"></script>


    <!-- leaflet -->
    <link rel = "stylesheet" href = "http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
    <script src = "http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>

    <!-- custom -->
    <script src="/static/js/custom.js"></script>

    <link rel="stylesheet" href="/static/css/custom.css">

</body>
</html>
