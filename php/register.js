$.ajax({
    url: "./php/session.php",
    type: "GET",
    success: function(data)
    {
        if(data['authorized'])
        {
            window.location.href = "index.html";
        }
    },
});

$(document).ready(function()
    {
        $('#signin-form').on("submit", function(e)
        {
            e.preventDefault();
            var $form = $(this);
            var $actionUrl = $form.attr('action');
            
            var $mail = $("#input-email").val();
            var $phone_number = $("#input-phone-number").val();
            var $first_name = $("#input-first-name").val();
            var $last_name = $("#input-last-name").val();
            var $password = $("#input-password").val();

            var $fields = [$mail, $phone_number, $first_name, $last_name, $password];

            $fields.forEach(function(field, index) {
                if (field.length == 0) 
                {
                    return display("Wszystkie pola muszą być wypełnione");
                }
            });

            if(!MAIL_REGEX.test($mail)) return display("Nieprawidłowy adres e-mail");
            if($mail.length > MAX_SIZE_MAIL) return display("Adres e-mail nie może mieć powyżej 64 znaków");
            if(!PHONE_NUMBER_REGEX.test($phone_number)) return display("Nieprawidłowy numer telefonu");
            if($phone_number.length > MAX_SIZE_PHONE_phone_number) return display("Numer telefonu nie może mieć ponad 20 znaków");
            if(!LETTERS_ONLY_REGEX.test($first_name)) return display("Wprowadzone imie jest nieprawidłowe");
            if($first_name.length > MAX_SIZE_FIRST_NAME) return display("Imie nie może mieć ponad 32 znaki");
            if(!LETTERS_ONLY_REGEX.test($last_name)) return display("Wprowadzone nazwisko jest nieprawidłowe");
            if($last_name.length > MAX_SIZE_LAST_NAME) return display("Naziwsko nie może mieć ponad 32 znaki");
            if(!NO_SPECIAL_SYMBOLS_REGEX.test($password)) return display("Hasło nie może zawierać znaków specjalnych.")
            if($password.length > MAX_SIZE_PASSWORD) return display("Hasło nie może mieć ponad 64 znaki");
          
            $.ajax({
                type: "POST",
                url: $actionUrl,
                data: $form.serialize(),
                success: function(data)
                {
                    console.log(data['message']);
                }
            });
        });
    });

function display($message)
{
    $("#message").html($message);
    return false;
}