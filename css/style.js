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