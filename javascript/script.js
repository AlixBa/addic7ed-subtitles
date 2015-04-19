(function () {
    $("#submit").click(function () {
        $("#input").empty();
        $("#output").empty();

        var filename = $("#filename").val();
        var url = "https://alixba-addic7ed-php.herokuapp.com/";

        if(filename != "") {
            url = url + "?filename=" + encodeURIComponent(filename);
        }

        $.getJSON(url, function (json) {
            $("#input").html("$ " + json.input);
            $("#output").html(json.output);
        })
    });

    $(document).ready(function() {
        $("form input").keydown(function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
                $("#submit").click();
                return false;
            }
        });
    });
})();