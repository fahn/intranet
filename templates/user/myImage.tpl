<div class="alert alert-danger" role="alert">
  Klappt noch nicht
</div>


<form id="form1" runat="server">
  <input type='file' id="imgInp" />
  <img id="my-image" src="#" />
</form>
<button id="use">Upload</button>
<img id="result" src="">



<script type="text/javascript">

{literal}
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#my-image').attr('src', e.target.result);
      var resize = new Croppie($('#my-image')[0], {
        viewport: { width: 100, height: 100 },
        boundary: { width: 300, height: 300 },
        showZoomer: false,
        enableResize: true,
        enableOrientation: true
      });
      $('#use').fadeIn();
      $('#use').on('click', function() {
        resize.result('base64').then(function(dataImg) {
          var data = [{ image: dataImg }, { name: 'myimgage.jpg' }];
          // use ajax to send data to php
          $('#result').attr('src', dataImg);
        })
      })
    }
    reader.readAsDataURL(input.files[0]);
  }
}

$("#imgInp").change(function() {
  readURL(this);
});

{/literal}

</script>
