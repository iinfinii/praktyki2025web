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
        var $str = 0;

        $(document).on("input",'#input-password' ,function() {
            var $password = $(this).val();
            
            if ($("#password-strength-container").length === 0) {
                let $container = $('<div>', { id: 'password-strength-container', style: "margin-top: 20px"});
            
                let $label = $('<label>', { text: 'Siła hasła:' });
        
                let $progressContainer = $('<div>', { class: 'progress tertiary', style: "border: 1px solid #cccccc; border-radius:10px;"});
                let $progressBar = $('<div>', {
                    class: 'progress-bar',
                    role: 'progressbar',
                    'aria-valuenow': '0',
                    'aria-valuemin': '0',
                    'aria-valuemax': '100',
                    style: 'width: 0%',
                    id: 'progress-bar'
                });
                let $message = $('<p>', { id: "str_msg"});
                $container.hide();
            
                $progressContainer.append($progressBar);
                $container.append($label).append($progressContainer).append($message);

                $("#input-password").after($container);
                $container.fadeIn(500);
            }

            $security = password_security($password);
            $str = $security.strength;

            let $color;
            if ($security.strength <= 5) {
                let $redToYellowRatio = $security.strength / 5;
                $color = `rgb(235, ${Math.round(66 + (140 * $redToYellowRatio))}, 66)`;
            } else {
                let $yellowToGreenRatio = ($security.strength - 5) / 5;
                $color = `rgb(${Math.round(240 - (220 * $yellowToGreenRatio))}, 206, ${Math.round(31 + (123 * $yellowToGreenRatio))})`;
            }

            $("#str_msg").html($security.message);
            $("#progress-bar").css({
                "width": ($security.strength * 10) + "%",
                "background-color": $color
            });
        });

        $('#signin-form').on("submit", function(e)
        {
            e.preventDefault();
            var $form = $(this);
            var $actionUrl = $form.attr('action');
            
            var $mail = $("#input-email").val();
            var $phone_number = $("#input-phone-number").val().replace(/\s+/g, '');
            var $first_name = $("#input-first-name").val();
            var $last_name = $("#input-last-name").val();
            var $password = $("#input-password").val(); 
            var $confirm_password = $("#input-confirm-password").val();

            if(!MAIL_REGEX.test($mail)) return display("Nieprawidłowy adres e-mail");
            if($mail.length > MAX_SIZE_MAIL) return display("Adres e-mail nie może mieć powyżej 64 znaków");
            if(!PHONE_NUMBER_REGEX.test($phone_number)) return display("Nieprawidłowy numer telefonu");
            if($phone_number.length > MAX_SIZE_PHONE_NUMBER) return display("Numer telefonu nie może mieć ponad 20 znaków");
            if(!LETTERS_ONLY_REGEX.test($first_name)) return display("Wprowadzone imie jest nieprawidłowe");
            if($first_name.length > MAX_SIZE_FIRST_NAME) return display("Imie nie może mieć ponad 32 znaki");
            if(!SURNAME_REGEX.test($last_name)) return display("Wprowadzone nazwisko jest nieprawidłowe");
            if($last_name.length > MAX_SIZE_LAST_NAME) return display("Naziwsko nie może mieć ponad 32 znaki");
            if(!NO_SPECIAL_SYMBOLS_REGEX.test($password)) return display("Hasło nie może zawierać znaków specjalnych.")
            if($password.length > MAX_SIZE_PASSWORD) return display("Hasło nie może mieć ponad 64 znaki");
            if($str < 5) return display("Zbyt słabe hasło");

            if($confirm_password.length === 0) return display("Powtórz hasło.");
            if($confirm_password != $password) return display("Wpisane hasła się nie zgadzają");
          
            $.ajax({
                type: "POST",
                url: $actionUrl,
                data: $form.serialize(),
                success: function(data)
                {
                    if(data['status'] == true)
                    {
                        $("#message").css("color", "green");
                        display("Konto zostało pomyślnie utworzone.");
                        
                        setTimeout(function () {
                            window.location.href = "login";
                        }, 2000);
                    }
                    else
                    {
                        display(data['message']);
                    }
                }
            });
        });
    });

function display($message)
{
    $("#message").html($message);
    return false;
}

function password_security($password)
{
    let $response = {
        "message": "Hasło powinno zawierać przynajmniej 5 znaków",
        "strength": 0
    };
    let $strength = 2;

    if($password.length < 3) 
    {
        $response["strength"] = 0;
        return $response;
    }
    if($password.length < 5 && $password.length >= 3)
    {
        $response["strength"] = 1;
        return $response;
    }

    
    if(/[!@#$%^&*()_+\-=\[\]{}|:\\;,.<>\/?]/.test($password))
    {
        $strength++;
    }
    else
    {
        $response["message"] = "Hasło powinno zawierać przynajmniej jeden symbol specjalny";
    }

    if(/[0-9]/.test($password))
    {
        $strength++;
    }
    else
    {
        $response["message"] = "Hasło powinno zawierać przynajmniej jedną cyfrę";
    }

    if(/[A-Z]/.test($password))
    {
        $strength++;
    } 
    else
    {
        $response["message"] = "Hasło powinno zawierać przynajmniej jeden wielki znak";
    }

    if($strength >= 5 && $strength < 10)
    {
        $strength += parseInt(($password.length - 8) / 4);
        
        $response["message"] = "OK";
        if($strength > 5) $response["message"] = "Dobre";
        if($strength > 7) $response["message"] = "Silne";
    }
    if ($strength >= 10) 
    {
        $strength = 10;
    }

    $response["strength"] = $strength;
    return $response;
}