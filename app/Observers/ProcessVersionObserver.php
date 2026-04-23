<?php

namespace App\Observers;

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProcessVersionObserver
 * @package App\Observers
 */
class ProcessVersionObserver {

    /**
     * Create an initial history for the new created process version.
     * @param ProcessVersion $processVersion
     * @return void
     */
    public function created(ProcessVersion $processVersion) {
        $processVersion->syncTemplatePreviewDatasets();
        $history = ProcessVersionHistory::makeInitial($processVersion);
        $processVersion->history()->save($history);
        $processVersion->update(['history_head' => $history->id]);
        $processVersion->regenerateCache();
    }

    /**
     * @param ProcessVersion $processVersion
     * @return void
     */
    public function deleting(ProcessVersion $processVersion) {
        $processVersion->environments()->delete();
        $processVersion->deletePreviewDatasets();
        $processVersion->clearHistory();
        $processVersion->flushCache();
        $processVersion->process->author->flushCache();

        // Delete physical files
        Storage::delete([
            $processVersion->dependenciesExportFilePath(),
            $processVersion->definitionExportFilePath()
        ]);
    }

    /**
     * @param ProcessVersion $processVersion
     * @return void
     */
    public function updated(ProcessVersion $processVersion) {
        $processVersion->regenerateCache();
    }

    /**
     * When a process version is published.
     * @param ProcessVersion $processVersion
     * @return void
     * @throws FileNotFoundException
     */
    public function published(ProcessVersion $processVersion) {
        // Refresh the model so that the updated (static) version is loaded.
        $processVersion->refresh();

        // Clear the user cache so that the "published_process_version_ids,.." regenerate.
        $processVersion->process->author->flushCache();

        // Delete all history for the published process version.
        $processVersion->clearHistory();

        // Update definition, graph and dependency export files.
        $processVersion->exportDefinition();
        $processVersion->exportDependencies();

        // Update definition, graph and dependency export files for the "latest" version.
        $processVersion->exportDefinition('latest');
        $processVersion->exportDependencies('latest');
    }

    /**
     * When a process version was rolled back to a previous version.
     * @param ProcessVersion $processVersion
     * @return void
     * @noinspection PhpUnused
     * @throws FileNotFoundException
     */
    public function rolledback(ProcessVersion $processVersion) {
        // Clear the user cache so that the "published_process_version_ids,.." regenerate.
        $processVersion->process->author->flushCache();

        // Sync the template preview datasets.
        $processVersion->syncTemplatePreviewDatasets();

        // Delete all history for the published process version and initialize new.
        $processVersion->clearHistory(true);

        // Update definition, graph and dependency export files.
        $processVersion->exportDefinition();
        $processVersion->exportDependencies();
    }

}
