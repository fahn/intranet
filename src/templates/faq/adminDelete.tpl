<h2>Faq löschen</h2>

<form action="" method="post">
    <input type="hidden" name="faqFormAction" value="Delete">
    <input type="hidden" name="faqFaqId" value="{$item.faqId}">
    <p>Titel: {$item.title}</p>

    <button type="submit" class="btn btn-danger">Delete</button>
</form>
