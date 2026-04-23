<?php

namespace App\Rules;

use App\Models\Process;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

/**
 * Prüft ob eine Prozesstyp-Version bereits existiert.
 * Class VersionExists
 * @package App\Rules
 */
class VersionExists implements ValidationRule {

    /**
     * Versionsnummer
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!$value instanceof UploadedFile) {
            $fail($this->message());
        }

        try {
            $content = File::get($value->getRealPath());
            $array = json_decode($content, true);

            if (!is_array($array)) {
                $fail($this->message());
            }

            $this->namespace = $array['namespace'];
            $this->identifier = $array['identifier'];
            $this->version = $array['version'];

            $existing = Process::whereFullNamespace($this->namespace . '/' . $this->identifier)->with('versions')->first();

            if (!$existing) {
                $fail($this->message());
            }

            if ($existing->graphs->firstWhere('version', '=', $this->version)) {
                $fail($this->message());
            }
        }
        catch (Exception) {
            $fail($this->message());
        }
    }

    /**
     * Get the validation error message.
     * @return string
     */
    public function message() {
        return 'Der Prozess "' . $this->namespace . '/' . $this->identifier . '" in der Version "' . $this->version . '" existiert bereits.';
    }
}
