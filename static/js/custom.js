$(document).ready(function() {
    //
    var filename = basename($(location).attr('pathname'));

    $('a[href$="'+ filename +'"]').addClass('active');

    function basename(path) {
       return path.split('/').reverse()[0];
    }

    // data
    $('select.js-example-basic-single').select2({
      placeholder: 'Bitte wählen',
      allowClear: true,
      minimumResultsForSearch: 1,
      hideSelectionInSingle: true,
     });


    $('select.js-example-basic-multiple').select2();

   if (typeof data !== 'undefined') {
      $(".js-example-data-array").select2({
        data: data
      })
    }



    $(".dropdown").hover(function(){
        $(this).addClass("show");
    },function(){
        $(this).removeClass("show");
    });

    /* html editor */
    $('#summernote').summernote({
        placeholder: '',
        tabsize: 2,
        height: 150,
        lang: 'de-DE',
        codeview: false,
      });

    /*
     *
    */
    $.fn.mirror = function (selector) {
        return this.each(function () {
            var $this = $(this);
            var $selector = $(selector);
            $this.bind('keyup', function () {
              val = $this.val();
              newval = 21;
              if(val >= 19) {
                if(val == 29) {
                  newval = 30;
                } else {
                  newval = +val + 2;
                }
              }
                $selector.val((newval));
            });
        });
    };

    $('#userRegisterGameSetA1Points').mirror('#userRegisterGameSetB1Points');
    $('#userRegisterGameSetA2Points').mirror('#userRegisterGameSetB2Points');
    $('#userRegisterGameSetA3Points').mirror('#userRegisterGameSetB3Points');


    // clone select2
    $('a.clonerow').click(function(){
      console.log("START");
      $("div.initline:first").cloneSelect2().appendTo('#containerClone');
      $("div.initline select").css('width', '100%');
      event.preventDefault();
      event.stopPropagation();
    });

    jQuery.fn.cloneSelect2 = function (withDataAndEvents, deepWithDataAndEvents) {
        var $oldSelects2 = this.is('select') ? this : this.find('select');
        $oldSelects2.select2('destroy');
        var $clonedEl = this.clone(withDataAndEvents, deepWithDataAndEvents);
        $oldSelects2.select2();
        $clonedEl.is('select') ? $clonedEl.select2() : $clonedEl.find('select').select2();
        return $clonedEl;
    };


    // Tooltipps
    $('[data-toggle="tooltip"]').tooltip();


    //$('select#rankingGamePlayerA1').val('Moritz Patzelt').trigger('change');
    //$('select#rankingGamePlayerA1').val('144').trigger('change');

    /*  DEBUG.STAGE ] */
    $("a").each(function() {
      var address = $(location).attr('href')
        if (address.includes("stage=debug")) {
            var $this = $(this);
            var _href = $(this).attr('href');
            if(_href.includes('?')) {
               var sep = "&";
            }else{
                var sep = "?";
            }
            $this.attr("href", _href + sep + 'stage=debug');
        }
    });




    $('#mySelector').change( function(){
      var selection = $(this).val();
      console.log(selection);
      $('table')[selection? 'show' : 'hide']();

      if (selection) {
        $.each($('#myTable tbody tr'), function(index, item) {
          $(item)[$(item).is(':contains('+ selection  +')')? 'show' : 'hide']();
        });
      } else {
        console.log("***");
          $.each($('#myTable tbody tr'), function(index, item) {
/*            console.log(item);
            $(this).show(); */
          });
      }

    });

    /* DATE_TIME_PICKER */
    $(".datetimepicker").flatpickr({
        weekNumbers: true,
        enableTime: true,
        time_24hr: true,
        dateFormat: "d.m.Y H:i",
        locale: "de",
        "locale": {
            "firstDayOfWeek": 1 // start week on Monday
        }
    });

    $(".datepicker").flatpickr({
        weekNumbers: true,
        dateFormat: "d.m.Y",
        "locale": {
            "firstDayOfWeek": 1 // start week on Monday
        }
    });

});
