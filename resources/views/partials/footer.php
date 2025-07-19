<footer class="bg-gray-800 text-white text-center py-6 mt-12">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-lg">&copy; <?= date('Y') ?> PHP MVC Framework. Built with ❤️</p>
        <div class="mt-2 space-x-4 text-sm">
            <a href="<?= Core\View\View::url('/') ?>" class="text-gray-300 hover:text-white transition-colors">Home</a>
            <span class="text-gray-500">|</span>
            <a href="<?= Core\View\View::url('/about') ?>" class="text-gray-300 hover:text-white transition-colors">About</a>
            <span class="text-gray-500">|</span>
            <a href="<?= Core\View\View::url('/contact') ?>" class="text-gray-300 hover:text-white transition-colors">Contact</a>
        </div>
    </div>
</footer>