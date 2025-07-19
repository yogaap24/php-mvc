<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to PHP MVC Framework</h1>
            <p class="text-xl text-gray-600 leading-relaxed">A modern, modular PHP MVC framework with clean architecture and best practices.</p>
        </div>

        <hr class="border-gray-200 my-8">

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">✨ Features</h3>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-center">
                        <span class="text-blue-500 mr-3">🏗️</span>
                        <span>Modular Architecture</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-500 mr-3">🔒</span>
                        <span>Security Built-in</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-purple-500 mr-3">📊</span>
                        <span>Database Abstraction</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-pink-500 mr-3">🎨</span>
                        <span>View Templating</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-yellow-500 mr-3">🚀</span>
                        <span>Easy Routing</span>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-4">🚀 Quick Start</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">Get started by exploring our authentication system or building your first module.</p>

                                <div class="space-y-3">
                    <a href="<?= Core\View\View::url('/users/register') ?>" class="inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-6 py-3 text-base bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500">
                        Get Started
                    </a>

                    <a href="#" class="inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-6 py-3 text-base border border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500 ml-4">
                        View Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Alert -->
    <div class="mt-8">
        <div class="bg-blue-50 border-blue-200 text-blue-800 border border-l-4 p-4 rounded-md relative" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">Welcome to the PHP MVC Framework! This is built with Tailwind CSS.</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()"
                            class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>