function textEvaluate(value)
{
    $(document).ready(function()
    {
        var text = "";
        if (value == 'random') {
            text = {
                text_evaluate: "random",
            };
        } else if (value == 'provided') {
            text = {
                text_evaluate: $("#text_evaluate").val(),
            };
        }

        $.ajax({
            type: "POST",
            url: 'stack.php',
            data: {
                text_json: JSON.stringify(text)
            },
            success: function(data)
            {
                $("#textEvaluate").html(data);
            }
        });
    });
}