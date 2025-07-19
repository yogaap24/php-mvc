<nav class="bg-primary text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="<?= Core\View\View::url('/') ?>" class="text-xl font-bold text-white hover:text-blue-200 transition-colors">
                    PHP MVC
                </a>
            </div>
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="<?= Core\View\View::url('/') ?>" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                    <?php if (Core\Middleware\AuthMiddleware::check()): ?>
                        <a href="<?= Core\View\View::url('/home/dashboard') ?>" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                        <a href="<?= Core\View\View::url('/users/profile') ?>" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Profile</a>
                        <a href="<?= Core\View\View::url('/users/logout') ?>" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="<?= Core\View\View::url('/users/login') ?>" class="text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                        <a href="<?= Core\View\View::url('/users/register') ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white hover:bg-blue-700 p-2 rounded-md">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?= Core\View\View::url('/') ?>" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Home</a>
                <?php if (Core\Middleware\AuthMiddleware::check()): ?>
                    <a href="<?= Core\View\View::url('/home/dashboard') ?>" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                    <a href="<?= Core\View\View::url('/users/profile') ?>" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Profile</a>
                    <a href="<?= Core\View\View::url('/users/logout') ?>" class="text-white block bg-red-600 hover:bg-red-700 px-3 py-2 rounded-md text-base font-medium">Logout</a>
                <?php else: ?>
                    <a href="<?= Core\View\View::url('/users/login') ?>" class="text-white block hover:bg-blue-700 px-3 py-2 rounded-md text-base font-medium">Login</a>
                    <a href="<?= Core\View\View::url('/users/register') ?>" class="text-white block bg-green-600 hover:bg-green-700 px-3 py-2 rounded-md text-base font-medium">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>