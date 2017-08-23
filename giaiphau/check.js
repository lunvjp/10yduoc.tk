var idSum;

function doMoreQuestion(id) {
    idSum = id;
    $("#choiceuser").empty();
    $("#ajax-load").css('display','block');
    $.ajax({
        url: 'check.php',
        type: 'POST',
        data: {
            id: id,
            action: 'dotest'
        },
        success: function(data) {
            $("#ajax-load").css('display','none');
            $("#choiceuser").html(data);
        }
    });
}

function seeResult(id) { // id = testid
    idSum = id;
    $("#choiceuser").empty();
    $("#ajax-load").css('display','block');
    $.ajax({
        url: "check.php",
        type: 'POST',
        data: {
            id: id,
            type: 'done'
        },success: function(data) {
            $("#ajax-load").css('display','none');
            $("#choiceuser").html(data);
        }
    });
}

function getLink(id) { // id = testid
    idSum = id;
}

$(function(){
    $("#yes").click(function(){
        $("#choiceuser").empty();
        $("#ajax-load").css('display','block');
        $.ajax({
            url: 'check.php',
            type: 'POST',
            data: {
                id: idSum,
                action: 'dotest'
            },
            success: function(data) {
                $("#ajax-load").css('display','none');
                $("#choiceuser").html(data);
            }
        });
    });
    $("#wrong-button").click(function () {
        $("#choiceuser").empty();
        $("#ajax-load").css('display','block');
        $.ajax({
            url: "check.php",
            type: "POST",
            data: {
                id: idSum,
                type: 'wronganswer'
            },
            success: function (data) {
                $("#ajax-load").css('display','none');
                $("#choiceuser").html(data);
            }
        });
    });

    $("#right-button").click(function () {
        $("#choiceuser").empty();
        $("#ajax-load").css('display','block');
        $.ajax({
            url: "check.php",
            type: "POST",
            data: {
                id: idSum,
                type: 'rightanswer'
            },
            success: function (data) {
                $("#ajax-load").css('display','none');
                $("#choiceuser").html(data);
            }
        });
    });

    $("#submit-button").click(function(){
        $("#form-do-test").submit();
    });
    $("input").click(function(){
        id = $(this).attr("class");
        temp = 'div#'+id;
        x = 'input'+'.'+id;

        setTimeout(function(){
            $(temp).hide();
        },200);
    });
});
