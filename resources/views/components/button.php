<?php
/**
 * Button Component (Tailwind CSS)
 *
 * Usage: <?= Core\View\View::renderPartial('components/button', ['text' => 'Click Me', 'type' => 'primary', 'size' => 'lg']) ?>
 *
 * @param string $text - Button text
 * @param string $type - primary, secondary, success, danger, warning, info (default: primary)
 * @param string $size - sm, lg (default: normal)
 * @param string $href - If provided, renders as link
 * @param string $onclick - JavaScript onclick handler
 * @param bool $disabled - Whether button is disabled
 * @param bool $outline - Whether to use outline style
 */

$text = $text ?? 'Button';
$type = $type ?? 'primary';
$size = $size ?? '';
$href = $href ?? null;
$onclick = $onclick ?? '';
$disabled = $disabled ?? false;
$outline = $outline ?? false;

// Base classes
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

// Size classes
$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm',
    '' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base'
];

// Color classes
if ($outline) {
    $colorClasses = [
        'primary' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
        'secondary' => 'border border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500',
        'success' => 'border border-green-600 text-green-600 hover:bg-green-50 focus:ring-green-500',
        'danger' => 'border border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500',
        'warning' => 'border border-yellow-600 text-yellow-600 hover:bg-yellow-50 focus:ring-yellow-500',
        'info' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500'
    ];
} else {
    $colorClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500'
    ];
}

$sizeClass = $sizeClasses[$size] ?? $sizeClasses[''];
$colorClass = $colorClasses[$type] ?? $colorClasses['primary'];

$classes = "{$baseClasses} {$sizeClass} {$colorClass}";

if ($disabled) {
    $classes .= ' opacity-50 cursor-not-allowed';
}

$attributes = '';
if ($onclick && !$disabled) $attributes .= " onclick=\"{$onclick}\"";
if ($disabled) $attributes .= " disabled";
?>

<?php if ($href && !$disabled): ?>
    <a href="<?= Core\View\View::escape($href) ?>" class="<?= $classes ?>"<?= $attributes ?>>
        <?= Core\View\View::escape($text) ?>
    </a>
<?php else: ?>
    <button type="button" class="<?= $classes ?>"<?= $attributes ?>>
        <?= Core\View\View::escape($text) ?>
    </button>
<?php endif; ?>