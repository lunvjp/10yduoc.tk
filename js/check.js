$(function () {
    $("#wrong-button").click(function () {
        $("#choiceuser").empty();
        id = $("#wrongsentence").val();
        $.ajax({
            url: "../check.php",
            type: "POST",
            data: {
                id: id,
                type: 'wronganswer'
            },
            success: function (data) {
                $("#choiceuser").html(data);
            }
        });
    });

    $("#right-button").click(function () {
        $("#choiceuser").empty();
        id = $("#wrongsentence").val();
        $.ajax({
            url: "../check.php",
            type: "POST",
            data: {
                id: id,
                type: 'rightanswer'
            },
            success: function (data) {
                $("#choiceuser").html(data);
            }
        });
    });
});