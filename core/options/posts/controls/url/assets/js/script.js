jQuery(document).ready(function ($) {
    var url_fields = $(".dm-input-url");
    var SubmitButton = document.getElementById("save-post") || false;
    var PublishButton = document.getElementById("publish") || false;
    if (SubmitButton) {
        SubmitButton.addEventListener("click", SubmCLICKED, false);
    }
    if (PublishButton) {
        PublishButton.addEventListener("click", SubmCLICKED, false);
    }

    function SubmCLICKED(e) {
        var passed = false;

        url_fields.each(function (index) {
            if (validURL($(this).val())) {
                passed = true;
            } else {
                alert("Insert valid url");
            }
            if (!passed) {
                e.preventDefault();
                return false;
            }
        });
    }

    function validURL(checkUrl) {
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ //port
        '(\\?[;&amp;a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i');
        return pattern.test(myURL);
     }
});