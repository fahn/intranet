$(document).ready(function() {
    /* NAVI active boots trap*/
    function basename(path) {
        return path.split("/").reverse()[0];
    }
    var filename = basename($(location).attr("pathname"));
    $('ul.nav a[href$="${filename}"]').addClass("active");




    // ??
    $(".dropdown").hover(function() {
        $(this).addClass("show");
    }, function() {
        $(this).removeClass("show");
    });

    /**
     * wsgyi editor
     */
    /* if (typeof summernote === "function") { */
        $("#summernote").summernote({
            placeholder: "",
            tabsize: 2,
            height: 150,
            lang: "de-DE",
            codeview: false,
            toolbar: [
                // [groupName, [list of button]]
                ["style", ["style", "bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough", "superscript", "subscript"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["insert", ["link"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["height", ["height"]]
                ]
        });
    //}



    // Tooltipps
    $('[data-toggle="tooltip"]').tooltip();


    $("#mySelector").change(function() {
        var selection = $(this).val();
        console.log(selection);
        $("table")[selection ? "show" : "hide"]();

        if (selection) {
            $.each($("#myTable tbody tr"), function(index, item) {
                $(item)[$(item).is(":contains(" + selection + ")") ? "show" : "hide"]();
            });
        } else {
            $.each($("#myTable tbody tr"), function(index, item) {
                /*            console.log(item);
                            $(this).show(); */
            });
        }

    });

    //if (typeof flatpickr === "function") {
        /* DATE_TIME_PICKER */
        $(".datetimepicker").flatpickr({
            allowInput: true,
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
            allowInput: true,
            weekNumbers: true,
            dateFormat: "d.m.Y",
            "locale": {
                "firstDayOfWeek": 1 // start week on Monday
            }
        });
    //}

    //if (typeof select2 === "function") {
    $(".js-data-ajax-player").select2({
        language: "de",
        CloseOnSelect: true,
        //allowClear: true,
        //minimumResultsForSearch: 1,
        //hideSelectionInSingle: true,
        minimumInputLength: 3,
        ajax: {
            url: "/ajax/player.php",
            type: "post",
            dataType: "json",
            data: function(params) {
                var query = {
                    playerSearch: params.term,
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
        },
        placeholder: "Search for a player",
        delay: 250,
        cache: true
    });

    $(".js-data-ajax-user").select2({
        language: "de",
        CloseOnSelect: true,
        //allowClear: true,
        //minimumResultsForSearch: 1,
        //hideSelectionInSingle: true,
        minimumInputLength: 3,
        ajax: {
            url: "/ajax/user.php",
            type: "post",
            dataType: "json",
            data: function(params) {
                var query = {
                    userSearch: params.term,
                };

                return query;
            },
            processResults: function(data) {
                return {
                    results: data.results
                };
            },
        },
        placeholder: "Search for a User",
        delay: 250,
        cache: true
    });

    // Select
    $("select.js-example-basic-single").select2({
        language: "de",
        placeholder: "Bitte wählen",
        allowClear: true,
        minimumResultsForSearch: 1,
        hideSelectionInSingle: true,
    });
});
