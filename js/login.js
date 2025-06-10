$.ajax({
    url: "./php/session.php",
    type: "GET",
    success: function(data)
    {
        if(data['authorized'])
        {
            window.location.href = "";
        }
    },
});

$(document).ready(function()
{
    $('#login-form').on("submit", function(e)
    {
        e.preventDefault();
                
        var $form = $(this);
        var $actionUrl = $form.attr('action');

        var $mail = $("#input-email").val();
        var $password = $("#input-password").val();

        if($mail.length == 0)
        {
            display("Adres e-mail nie może być pusty.");
            return;
        }
        if(!MAIL_REGEX.test($mail) || $mail.length > MAX_SIZE_MAIL)
        {
            display("Nieprawidłowy adres e-mail.");
            return;
        }
    

        if($password.length == 0)
        {
            display("Hasło nie może być puste.");
            return;
        }
        if(!NO_SPECIAL_SYMBOLS_REGEX.test($password) || $password.length > MAX_SIZE_PASSWORD)
        {
            display("Hasło nie może zawierać znaków specjalnych.");
            return;
        }
        

        $.ajax({
            type: "POST",
            url: $actionUrl,
            data: $form.serialize(),
            success: function(data)
            {
                if(data['status'] == true)
                {
                    window.location.href = "index";
                }
                else
                {
                    display("Hasło lub e-mail jest niepoprawne.");
                }
            }
        });
    });
});

function display($message)
{
    $("#message").html($message);
}