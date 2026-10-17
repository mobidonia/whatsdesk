<?php

namespace Modules\Setup\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Modules\Setup\Services\DatabaseTester;
use Modules\Setup\Services\EnvWriter;

class SetupController extends Controller
{
    public function __construct(
        protected EnvWriter $envWriter,
        protected DatabaseTester $databaseTester,
    ) {}

    /**
     * Check if setup is already complete.
     */
    protected function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }

    /**
     * Redirect to home if already installed.
     */
    protected function redirectIfInstalled(): ?RedirectResponse
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return null;
    }

    /**
     * Step 1: Welcome screen.
     */
    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        return view('setup::welcome', [
            'currentStep' => 0,
        ]);
    }

    /**
     * Step 2: Site configuration form.
     */
    public function site(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        // Get the current URL from the request (not from .env)
        $currentUrl = $this->getCurrentSiteUrl();

        $existingValues = [
            'app_name' => $this->envWriter->get('APP_NAME') ?: config('app.name'),
            'app_url' => $currentUrl,
        ];

        return view('setup::site', [
            'currentStep' => 1,
            'values' => $existingValues,
            'isWritable' => $this->envWriter->isWritable(),
        ]);
    }

    /**
     * Step 2: Save site configuration.
     */
    public function saveSite(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
        ]);

        // Save to .env
        try {
            $this->envWriter->update([
                'APP_NAME' => $validated['app_name'],
                'APP_URL' => rtrim($validated['app_url'], '/'),
            ]);
        } catch (Exception $e) {
            return redirect()
                ->route('setup.site')
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('setup.database')
            ->with('success', 'Site configuration saved successfully!');
    }

    /**
     * Get the current site URL from the request.
     */
    protected function getCurrentSiteUrl(): string
    {
        $scheme = request()->secure() ? 'https' : 'http';
        $host = request()->getHost();
        $port = request()->getPort();

        $url = "{$scheme}://{$host}";

        // Add port if non-standard
        if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $url .= ":{$port}";
        }

        return $url;
    }

    /**
     * Step 3: Database configuration form.
     */
    public function database(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        // Pre-fill with existing values if any
        $existingValues = [
            'host' => $this->envWriter->get('DB_HOST') ?: '127.0.0.1',
            'port' => $this->envWriter->get('DB_PORT') ?: '3306',
            'database' => $this->envWriter->get('DB_DATABASE') ?: '',
            'username' => $this->envWriter->get('DB_USERNAME') ?: '',
            'password' => '', // Never pre-fill password for security
        ];

        return view('setup::database', [
            'currentStep' => 2,
            'values' => $existingValues,
            'isWritable' => $this->envWriter->isWritable(),
        ]);
    }

    /**
     * Step 3: Save database configuration.
     */
    public function saveDatabase(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        $validated = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|string|max:10',
            'database' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        // Test the connection first
        $testResult = $this->databaseTester->test([
            'host' => $validated['host'],
            'port' => $validated['port'],
            'database' => $validated['database'],
            'username' => $validated['username'],
            'password' => $validated['password'] ?? '',
        ]);

        if (! $testResult['success']) {
            return redirect()
                ->route('setup.database')
                ->withInput()
                ->with('error', $testResult['message']);
        }

        // Save to .env
        try {
            $this->envWriter->update([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $validated['host'],
                'DB_PORT' => $validated['port'],
                'DB_DATABASE' => $validated['database'],
                'DB_USERNAME' => $validated['username'],
                'DB_PASSWORD' => $validated['password'] ?? '',
            ]);
        } catch (Exception $e) {
            return redirect()
                ->route('setup.database')
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('setup.finalize')
            ->with('success', 'Database configuration saved successfully!');
    }

    /**
     * Step 4: Finalize installation.
     */
    public function finalize(): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        return view('setup::finalize', [
            'currentStep' => 3,
            'isStorageWritable' => is_writable(storage_path()),
        ]);
    }

    /**
     * Step 4: Run migrations, seed, and complete installation.
     */
    public function complete(): RedirectResponse
    {
        if ($redirect = $this->redirectIfInstalled()) {
            return $redirect;
        }

        try {
            // Clear configuration cache first (after any env changes)
            Artisan::call('config:clear');

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Run seeders
            Artisan::call('db:seed', ['--force' => true]);

            // Module seeders
            Artisan::call('app:migrrate-modules');

            // Create the installed marker file
            $installedFile = storage_path('installed');
            $result = file_put_contents($installedFile, json_encode([
                'installed_at' => now()->toIso8601String(),
                'version' => config('app.version', '1.0.0'),
            ]));

            if ($result === false) {
                throw new Exception('Could not create the installed marker file. Please check storage directory permissions.');
            }

            // The login page sends visitors to register while there are no users,
            // and the first registered user becomes the admin.
            return redirect('/login')
                ->with('success', 'Installation completed successfully! Welcome to '.config('app.name').'!');

        } catch (Exception $e) {
            return redirect()
                ->route('setup.finalize')
                ->with('error', 'Installation failed: '.$e->getMessage());
        }
    }
}
