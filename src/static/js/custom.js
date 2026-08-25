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


        const calendar = new Calendar('#calendar', {
            defaultView: 'week',
            template: {
              time(event) {
                const { start, end, title } = event;
          
                return `<span style="color: white;">${formatTime(start)}~${formatTime(end)} ${title}</span>`;
              },
              allday(event) {
                return `<span style="color: gray;">${event.title}</span>`;
              },
            },
            calendars: [
              {
                id: 'cal1',
                name: 'Personal',
                backgroundColor: '#03bd9e',
              },
              {
                id: 'cal2',
                name: 'Work',
                backgroundColor: '#00a9ff',
              },
            ],
          });



          var Calendar = window.tui.Calendar;

          var cal = new Calendar('#app', {
            defaultView: 'month',
            calendars: MOCK_CALENDARS,
          });
        
    }

);
 