<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use ZipArchive;

class BackupController extends Controller
{
    public function index(): View
    {
        $this->adminOnly();

        return view('settings.backup');
    }

    public function downloadLanguageBackup()
    {
        $this->adminOnly();

        $langPath = base_path('lang');
        $backupName = date('Y-m-d_H-i-s').'_lang.zip';
        $tempPath = storage_path('app/temp/'.$backupName);

        // Create temp directory if it doesn't exist
        if (! File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;

        if ($zip->open($tempPath, ZipArchive::CREATE) === true) {
            // Get all files in lang directory recursively
            $files = File::allFiles($langPath);

            foreach ($files as $file) {
                $relativePath = str_replace($langPath.DIRECTORY_SEPARATOR, '', $file->getRealPath());
                $zip->addFile($file->getRealPath(), $relativePath);
            }

            $zip->close();

            // Download the file and delete it after download
            return response()->download($tempPath,$backupName,[
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        }

        return redirect()->route('admin.backup.index',)->withError(__('There was an error creating the backup.'));
    }

    public function restoreLanguages(Request $request): RedirectResponse
    {
        $this->adminOnly();

        $request->validate([
            'language_backup' => 'required|file|mimes:zip',
        ]);

        $path = $request->language_backup->storeAs('appupload', $request->language_backup->getClientOriginalName());
        $fullPath = storage_path('app/'.$path);

        $zip = new ZipArchive;

        if ($zip->open($fullPath)) {
            // Extract to lang directory
            $destination = base_path('lang');

            // Extract file
            $zip->extractTo($destination);

            // Close ZipArchive
            $zip->close();

            // Clean up uploaded file
            File::delete($fullPath);

            return redirect()->route('admin.backup.index')->withStatus(__('Language pack has been restored successfully.'));
        } else {
            return redirect()->route('admin.backup.index')->withError(__('There was an error restoring the language pack. Please try again.'));
        }
    }

}
