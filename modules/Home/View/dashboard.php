<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome to your dashboard! Manage your account and explore the features.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- User Profile Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Profile</h3>
                    <p class="text-gray-600">Manage your account</p>
                </div>
            </div>
            <a href="<?= Core\View\View::url('/users/profile') ?>"
               class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                View Profile
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- Security Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                    <p class="text-gray-600">Change password</p>
                </div>
            </div>
            <a href="<?= Core\View\View::url('/users/password') ?>"
               class="inline-flex items-center text-green-600 hover:text-green-700 font-medium">
                Manage Security
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <!-- System Info Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">System</h3>
                    <p class="text-gray-600">Framework info</p>
                </div>
            </div>
            <div class="text-purple-600 font-medium">
                PHP MVC Framework v1.0
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Welcome to Your Dashboard!</h2>
        <p class="text-blue-100 leading-relaxed">
            You have successfully logged into the PHP MVC Framework. This dashboard provides you with quick access to
            your account settings and system features. Explore the navigation above to access different sections of the application.
        </p>
        <div class="mt-6 flex flex-wrap gap-4">
            <a href="<?= Core\View\View::url('/users/profile') ?>"
               class="bg-white text-blue-600 px-6 py-2 rounded-md font-medium hover:bg-blue-50 transition-colors">
                Edit Profile
            </a>
            <a href="<?= Core\View\View::url('/users/logout') ?>"
               class="bg-red-500 text-white px-6 py-2 rounded-md font-medium hover:bg-red-600 transition-colors">
                Logout
            </a>
        </div>
    </div>

    <!-- System Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">1</div>
            <div class="text-gray-600 mt-1">Active User</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600"><?= date('H:i') ?></div>
            <div class="text-gray-600 mt-1">Current Time</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-purple-600"><?= date('Y-m-d') ?></div>
            <div class="text-gray-600 mt-1">Today's Date</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">PHP</div>
            <div class="text-gray-600 mt-1">Framework</div>
        </div>
    </div>
</div>