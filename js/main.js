
$.ajax({
    url: "./php/session.php",
    type: "GET",
    success: function(data)
    {
        console.log(data);
        if(data['authorized'] == false)
        {
            window.location.href = "signin";
            localStorage.removeItem("last_visited");
        }
        else
        {
            $(document).ready(function()
            {   
                $("#loading").fadeOut("slow");
            });
        }
    },
});

load_style();
$(document).ready(function()
{
    main();
});

function main()
{
    $("a").on("click", function(e)
    {
        if ($(this).hasClass("docs")) return;

        e.preventDefault();
        if($(this).attr("href") == "#") return;

        loadContent($(this).attr("href"));
    });

    $("#logout").on("click", function(e)
    {
        $.ajax({
            url: "./php/session.php",
            type: "LOGOUT",
            success: function(data)
            {
                window.location.href = "signin";
                localStorage.removeItem("last_visited");
            }
            });
    });

    if(localStorage.getItem("last_visited") != null)
    {
        loadContent(localStorage.getItem("last_visited"));
    }
    else
    {
        loadContent("views/main.html");
    }
    
}

function loadContent($path)
{
    $(".content").empty();
    $(".content").load($path);

    localStorage.setItem("last_visited", $path);
}

function load_style()
{
    $saved_theme = localStorage.getItem("theme");
    if($saved_theme == null)
    {
        $saved_theme = "light";
        localStorage.setItem("theme", $saved_theme);
    }

    $href = "css/colorsheets/" + $saved_theme + ".css";

    var style = document.createElement('link');
    style.rel = 'stylesheet';
    style.type = 'text/css';
    style.id = "theme";
    style.href = $href;
    document.getElementsByTagName('head')[0].appendChild(style);
}