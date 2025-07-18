<?php
/**
 * Alert Component (Tailwind CSS)
 *
 * Usage: <?= Core\View\View::renderPartial('components/alert', ['type' => 'success', 'message' => 'Success!']) ?>
 *
 * @param string $type - success, danger, warning, info
 * @param string $message - Alert message
 * @param bool $dismissible - Whether alert can be dismissed (default: true)
 */

$type = $type ?? 'info';
$message = $message ?? '';
$dismissible = $dismissible ?? true;

if (!$message) return;

// Tailwind color mapping
$alertColors = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'danger' => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'info' => 'bg-blue-50 border-blue-200 text-blue-800'
];

$iconColors = [
    'success' => 'text-green-400',
    'danger' => 'text-red-400',
    'warning' => 'text-yellow-400',
    'info' => 'text-blue-400'
];

$colorClass = $alertColors[$type] ?? $alertColors['info'];
$iconColor = $iconColors[$type] ?? $iconColors['info'];
?>

<div class="<?= $colorClass ?> border border-l-4 p-4 rounded-md <?= $dismissible ? 'relative' : '' ?>" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <!-- Icon based on type -->
            <?php if ($type === 'success'): ?>
                <svg class="h-5 w-5 <?= $iconColor ?>" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            <?php elseif ($type === 'danger'): ?>
                <svg class="h-5 w-5 <?= $iconColor ?>" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            <?php elseif ($type === 'warning'): ?>
                <svg class="h-5 w-5 <?= $iconColor ?>" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            <?php else: ?>
                <svg class="h-5 w-5 <?= $iconColor ?>" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            <?php endif; ?>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium"><?= Core\View\View::escape($message) ?></p>
        </div>
        <?php if ($dismissible): ?>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()"
                        class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>