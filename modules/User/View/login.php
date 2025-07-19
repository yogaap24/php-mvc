<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 text-center">Login</h2>
            <p class="text-gray-600 text-center mt-2">Welcome back! Please sign in to your account.</p>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $field => $error): ?>
                        <li class="text-sm"><?= is_string($error) ? htmlspecialchars($error) : htmlspecialchars($field . ': ' . (is_array($error) ? implode(', ', $error) : $error)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= Core\View\View::url('/users/login') ?>" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                    Sign In
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-600">
                Don't have an account?
                <a href="<?= Core\View\View::url('/users/register') ?>" class="text-blue-600 hover:text-blue-700 font-medium">
                    Register here
                </a>
            </p>
        </div>
    </div>
</div>