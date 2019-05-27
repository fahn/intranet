$(document).ready(function() {
    //
    var filename = basename($(location).attr('pathname'));

    $('a[href$="' + filename + '"]').addClass('active');

    function basename(path) {
        return path.split('/').reverse()[0];
    }



    $(".dropdown").hover(function() {
        $(this).addClass("show");
    }, function() {
        $(this).removeClass("show");
    });

    /**
     * wsgyi editor
     */
    if (typeof summernote === "function") {
        /* html editor */
        $('#summernote').summernote({
            placeholder: '',
            tabsize: 2,
            height: 150,
            lang: 'de-DE',
            codeview: false,
        });
    }



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
            if (_href.includes('?')) {
                var sep = "&";
            } else {
                var sep = "?";
            }
            $this.attr("href", _href + sep + 'stage=debug');
        }
    });




    $('#mySelector').change(function() {
        var selection = $(this).val();
        console.log(selection);
        $('table')[selection ? 'show' : 'hide']();

        if (selection) {
            $.each($('#myTable tbody tr'), function(index, item) {
                $(item)[$(item).is(':contains(' + selection + ')') ? 'show' : 'hide']();
            });
        } else {
            console.log("***");
            $.each($('#myTable tbody tr'), function(index, item) {
                /*            console.log(item);
                            $(this).show(); */
            });
        }

    });

    if (typeof flatpickr === "function") {
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
    }

    //if (typeof select2 === "function") {
        $('.js-data-ajax-player').select2({
            language: "de",
            CloseOnSelect: true,
            //allowClear: true,
            //minimumResultsForSearch: 1,
            //hideSelectionInSingle: true,
            minimumInputLength: 3,
            ajax: {
                url: '/ajax/player.php',
                type: "post",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        playerSearch: params.term,
                    }
                    return query;
                },
                processResults: function(data) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    //console.log(data);
                    return {
                        results: data.results
                    };
                },
            },
            placeholder: 'Search for a player',
            delay: 250,
            cache: true
        });

        // Select
        $('select.js-example-basic-single').select2({
            language: "de",
            placeholder: 'Bitte wählen',
            allowClear: true,
            minimumResultsForSearch: 1,
            hideSelectionInSingle: true,
        });
});
