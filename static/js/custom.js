$('.datepicker').datepicker({
    format: 'dd.mm.yyyy',
    language: "de",
    autoclose: true,
    todayHighlight: true,
});

$('.date').datepicker({
    format: 'dd.mm.yyyy',
    autoclose: true,
    autoclose: true,
    todayHighlight: true,
});

$('.select-selectize').selectize({
//  create: true,
  sortField: 'text'
});


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

$('.p11').mirror('.p12');
$('.p21').mirror('.p22');
$('.p31').mirror('.p32');

$('.p12').mirror('.p11');
$('.p22').mirror('.p21');
$('.p32').mirror('.p31');



$('#switch-gender').bootstrapSwitch('onText', 'Male');
$('#switch-gender').bootstrapSwitch('offText', 'Female');

$('a.clonerow').click(function(){
  console.log("START");

   $("div.initline")
    .last()
    .clone()
    .insertAfter($("div.initline").last())
    .find("select").attr("name",function(i,oldVal) {
         return oldVal.replace(/\[(\d+)\]/,function(_,m){
            return "[" + (+m + 1) + "]";
        });
    });

    return false;

    var $div = $(this).next();
    $div.find("div.initline select")
        .last()
        .clone()
        .appendTo($("div.initline").last())
        .val("")
        .attr("id",function(i,oldVal) {
            return oldVal.replace(/\d+/,function(m){
                return (+m + 1);
            });
        });
    return false;

});

$( function() {
  //$('[data-toggle=tooltip]').tooltip({placement: 'bottom',trigger: 'manual'}).tooltip('show');
  //$('[data-toggle=tooltip]').tooltip();
});
