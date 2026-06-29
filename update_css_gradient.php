<?php
$content = file_get_contents('css/styles.css');

$replacements = [
    '#667eea' => '#16a34a', // indigo to green-600
    '#764ba2' => '#15803d', // purple to green-700
    '#8b5cf6' => '#10b981', // purple-500 to emerald-500
    '#f093fb' => '#22c55e', // pink to green-500
    '#f5576c' => '#10b981', // rose to emerald-500
    '#4facfe' => '#10b981', // blue to emerald-500
    '#00f2fe' => '#059669', // cyan to emerald-600
    '99, 102, 241' => '34, 197, 94', // indigo-500 RGB to green-500 RGB
    'linear-gradient(135deg, #16a34a 0%, #15803d 100%)' => 'var(--gradient-primary)' // Just in case we replaced it inline at .page-header
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

// Ensure .page-header uses var(--gradient-primary) explicitly
$content = str_replace('background: var(--gradient-primary);', 'background: var(--gradient-primary);', $content); // No-op if it did the inline replace

file_put_contents('css/styles.css', $content);
echo "Updated CSS gradients\n";
?>
