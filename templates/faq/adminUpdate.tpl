<form>
  <div class="form-group">
    <label for="exampleInputEmail1">Titel1</label>
    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Kategorie</label>
    <select class="form-control" id="exampleFormControlSelect1">
      {html_options options=$FaqCategoryHtmlOptions selected=$customer_id}
    </select>
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Text</label>
    <textarea id="summernote" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
