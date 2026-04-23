<?php

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use App\ProcessType\Output;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

return new class extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                if ($this->isValidUserIdentityType($definition)) {
                    if (is_null($definition->outputByName('email'))) {
                        $emailOutput = new Output([
                            'name' => 'email',
                            'description' => 'E-mail',
                            'default' => '',
                            'type' => 'basic',
                            'type_options' => [],
                            'validation' => [],
                        ]);

                        $definition->outputs->add($emailOutput);
                        $version->update(['definition' => $definition->toArray()]);
                        $version->exportDefinition();

                        if ($version->process->latest_published_version_id === $version->id) {
                            $version->exportDefinition('latest');
                        }
                    }
                }

            }
        });

        ProcessVersionHistory::query()->with('processVersion')->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;

                if ($this->isValidUserIdentityType($definition)) {
                    if (is_null($definition->outputByName('email'))) {
                        $emailOutput = new Output([
                            'name' => 'email',
                            'description' => 'E-mail',
                            'default' => '',
                            'type' => 'basic',
                            'type_options' => [],
                            'validation' => [],
                        ]);

                        $definition->outputs->add($emailOutput);
                        $history->update(['definition' => $definition->toArray()]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        ProcessVersion::query()->chunk(10, function (Collection $versions) {
            /* @var ProcessVersion $version */
            foreach ($versions as $version) {
                $definition = $version->definition;

                if ($this->isValidUserIdentityTypeNew($definition)) {
                    $definition->outputs = $definition->outputs->filter(fn(Output $output) => $output->name !== 'email');

                    $version->update(['definition' => $definition->toArray()]);
                    $version->exportDefinition();

                    if ($version->process->latest_published_version_id === $version->id) {
                        $version->exportDefinition('latest');
                    }
                }
            }
        });

        ProcessVersionHistory::query()->chunk(10, function (Collection $histories) {
            /* @var ProcessVersionHistory $history */
            foreach ($histories as $history) {
                $definition = $history->definition;

                if ($this->isValidUserIdentityTypeNew($definition)) {
                    $definition->outputs = $definition->outputs->filter(fn(Output $output) => $output->name !== 'email');
                    $history->update(['definition' => $definition->toArray()]);
                }
            }
        });
    }

    /**
     * Check whether the process type is a valid user process identity type (old version).
     * @param Definition $definition
     * @return bool
     */
    public function isValidUserIdentityType(Definition $definition) {
        $outputNames = $definition->outputs->pluck('name');
        $requiredEnglish = collect(['first_name', 'last_name', 'user']);
        $requiredGerman = collect(['vorname', 'nachname', 'benutzer']);

        $validEnglish = true;
        $validGerman = true;

        foreach ($requiredEnglish as $item) {
            if (!$outputNames->contains($item)) {
                $validEnglish = false;
            }
        }

        foreach ($requiredGerman as $item) {
            if (!$outputNames->contains($item)) {
                $validGerman = false;
            }
        }

        return $validEnglish || $validGerman;
    }

    /**
     * Check whether the process type is a valid user process identity type (old version).
     * @param Definition $definition
     * @return bool
     */
    public function isValidUserIdentityTypeNew(Definition $definition) {
        $outputNames = $definition->outputs->pluck('name');
        $requiredEnglish = collect(['first_name', 'last_name', 'user', 'email']);
        $requiredGerman = collect(['vorname', 'nachname', 'benutzer', 'email']);

        $validEnglish = true;
        $validGerman = true;

        foreach ($requiredEnglish as $item) {
            if (!$outputNames->contains($item)) {
                $validEnglish = false;
            }
        }

        foreach ($requiredGerman as $item) {
            if (!$outputNames->contains($item)) {
                $validGerman = false;
            }
        }

        return $validEnglish || $validGerman;
    }
};
