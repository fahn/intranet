$(document).ready(
    function () {
        /* NAVI active boots trap*/
        function basename(path)
        {
            return path.split("/").reverse()[0];
        }
        var filename = basename($(location).attr("pathname"));
        $("ul.nav a[href$=\"${filename}\"]").addClass("active");

        // ??
        $(".dropdown").hover(
            function () {
                $(this).addClass("show");
            },
            function () {
                $(this).removeClass("show");
            }
        );

        /**
         * wsgyi editor
         */
        /* if (typeof summernote === "function") { */
        $("#summernote").summernote(
            {
                placeholder: "",
                tabsize: 2,
                height: 200,
                lang: "de-DE",
                codeview: false,
                toolbar: [
                    // [groupName, [list of button]]
                    ["style", ["bold", "italic", "underline", "clear"]],
                    ["font", ["strikethrough", "superscript", "subscript"]],
                    ["fontsize", ["fontsize"]],
                    ["color", ["color"]],
                    ["insert", ["link"]],
                    ["para", ["ul", "ol",]]
                ]
            }
        );
        // }
        // Tooltipps
        $("[data-toggle=\"tooltip\"]").tooltip();


        $("#mySelector").change(
            function () {
                var selection = $(this).val();
                $("table")[selection ? "show" : "hide"]();

                if (selection) {
                    $.each(
                        $("#myTable tbody tr"),
                        function (index, item) {
                            $(item)[$(item).is(":contains(" + selection + ")") ? "show" : "hide"]();
                        }
                    );
                } else {
                    $.each(
                        $("#myTable tbody tr"),
                        function (index, item) {
                            /*            console.log(item);
                                    $(this).show(); */
                        }
                    );
                }
            }
        );

        // if (typeof flatpickr === "function") {
        /* DATE_TIME_PICKER */
        $(".datetimepicker").flatpickr(
            {
                allowInput: true,
                weekNumbers: true,
                enableTime: true,
                time_24hr: true,
                dateFormat: "d.m.Y H:i",
                locale: "de",
                "locale": {
                    "firstDayOfWeek": 1
                    // start week on Monday
                }
            }
        );

        $(".datepicker").flatpickr(
            {
                allowInput: true,
                weekNumbers: true,
                dateFormat: "d.m.Y",
                "locale": {
                    "firstDayOfWeek": 1
                    // start week on Monday
                }
            }
        );
        // }
        // if (typeof select2 === "function") {
        $(".js-data-ajax").select2(
            {
                language: "de",
                CloseOnSelect: true,
                //allowClear: true,
                //minimumResultsForSearch: 1,
                //hideSelectionInSingle: true,
                minimumInputLength: 3,
                ajax: {
                    url: "/ajax/index.php?search=player",
                    type: "post",
                    dataType: "json",
                    data: function (params) {
                        var query = {
                            term: params.term,
                            data: this.data("item"),
                        };
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                },
                placeholder: "Search ...",
                delay: 250,
                cache: true
            }
        );

        // Select
        $("select.js-example-basic-single").select2(
            {
                language: "de",
                placeholder: "Bitte wählen",
                allowClear: true,
                minimumResultsForSearch: 1,
                hideSelectionInSingle: true,
            }
        );
    

        var mapOptions = {
        center: [52.2056804, 8.048224,17],
        zoom: 10
        }
        
        // Creating a map object
        var map = new L.map('map', mapOptions);
        
        // Creating a Layer object
        var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
        
        // Adding layer to the map
        map.addLayer(layer);

    }

);
 