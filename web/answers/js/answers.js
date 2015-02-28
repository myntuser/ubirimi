$('document').ready(function () {
    $('#btnEditDomain').click(function (event) {
        event.preventDefault();
        if (selected_rows.length == 1)
            document.location.href = '/answers/domain/edit/' + selected_rows[0];
    });
});