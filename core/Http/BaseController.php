<?php

namespace Core\Http;

use Core\Middleware\AuthMiddleware;
use Core\View\View;
use Core\Support\FlashMessage;

abstract class BaseController
{
	/**
	 * Handle response based on request type (API vs Web)
	 */
	protected function handleResponse(Request $request, $result, array $webOptions = []): Response
	{
		if ($request->isApiRequest()) {
			// Return JSON response for API requests
			return new Response($result->data, $result->code);
		}

		// Handle web requests
		if ($result->code >= 200 && $result->code < 300) {
			// Success case
			if (isset($webOptions['success_redirect'])) {
				// Set success flash message
				if (isset($webOptions['success_message'])) {
					FlashMessage::success($webOptions['success_message']);
				} else {
					FlashMessage::success($result->message ?? 'Operation completed successfully');
				}

				// Set redirect header
				header('Location: ' . $webOptions['success_redirect']);

				// Backup JavaScript redirect for browsers that don't follow header redirects
				echo '<!DOCTYPE html>
						<html>
						<head>
							<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($webOptions['success_redirect']) . '">
							<script>window.location.href = "' . htmlspecialchars($webOptions['success_redirect']) . '";</script>
						</head>
						<body>
							<p>Redirecting...</p>
							<p>If you are not redirected automatically, <a href="' . htmlspecialchars($webOptions['success_redirect']) . '">click here</a>.</p>
						</body>
						</html>';
				exit;
			}

			if (isset($webOptions['success_view'])) {
				return View::render($webOptions['success_view'], $webOptions['success_data'] ?? []);
			}

			// Success fallback - return JSON
			return new Response($result->data, $result->code);
		}

		// Error case (non-2xx status codes)
		if (isset($webOptions['error_view'])) {
			$errorMessage = $result->message ?? 'Operation failed';
			$errorData = $result->data;

			// Format error messages for display
			if (is_array($errorData)) {
				$errors = $errorData;
			} else {
				$errors = [$errorMessage];
			}

			$viewData = array_merge($webOptions['error_data'] ?? [], [
				'errors' => $errors,
				'old' => $webOptions['old_input'] ?? []
			]);

			return View::render($webOptions['error_view'], $viewData);
		}

		if (isset($webOptions['error_redirect'])) {
			// Set error flash message
			FlashMessage::error($result->message ?? 'Operation failed');
			header('Location: ' . $webOptions['error_redirect']);
			exit;
		}

		// Fallback - return JSON
		return new Response($result->data, $result->code);
	}

	/**
	 * Redirect helper
	 */
	protected function redirect(string $url): void
	{
		header('Location: ' . $url);
		exit;
	}

	/**
	 * Get old input values for form repopulation
	 */
	protected function getOldInput(Request $request, array $fields = []): array
	{
		$old = [];
		foreach ($fields as $field) {
			$old[$field] = $request->getPost($field, '');
		}
		return $old;
	}

	/**
	 * Get current authenticated user
	 * Note: Only use this in routes protected by AuthMiddleware
	 */
	protected function getCurrentUser(): array
	{
		return AuthMiddleware::user();
	}
}
