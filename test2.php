<?php
$content = file_get_contents('app/Http/Controllers/Admin/AdminUserController.php');
$pattern = '/(<(a|button)(?![^>]*\btitle=)[^>]*>)\s*(<i[^>]*data-feather="edit-2"[^>]*><\/i>)(.*?)(<\/\2>)/is';
if (preg_match_all($pattern, $content, $matches)) {
    print_r($matches[0]);
} else {
    echo "No match\n";
}
