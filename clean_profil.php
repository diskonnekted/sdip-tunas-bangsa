<?php
$content = file_get_contents('profil.php');

// Remove corrupted characters "âœ" or "âœ”" (often bytes E2 9C 94 when doubly-encoded, or C3 A2 C5 93 etc)
// Just regex out any non-ascii characters before the <span> or <i> in the Misi list
// Specifically, let's look for "flex items-start" and clean it up.
$content = preg_replace('/<li class="flex items-start">[^<]*<i/', '<li class="flex items-start"><i', $content);

// Let's also remove any garbage between </li> and <li> if any
file_put_contents('profil.php', $content);
echo "Cleaned profil.php\n";
?>
