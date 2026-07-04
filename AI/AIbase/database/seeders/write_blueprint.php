<?php
$content = file_get_contents("resources/views/student/blueprint_template.txt");
file_put_contents("resources/views/student/blueprint.blade.php", $content);
echo "done";
