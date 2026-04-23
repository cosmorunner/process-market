<?php /** @noinspection PhpUnused */

namespace App\Models;

use App\ProcessType\Definition;
use Database\Factories\ProcessVersionHistoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ProcessVersionHistory
 * @mixin Eloquent
 * @property string $id
 * @property string $process_versions_id
 * @property string|null $command
 * @property array|null $command_payload
 * @property array $calculated
 * @property array|Definition $definition
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static ProcessVersionHistoryFactory factory(...$parameters)
 * @method static Builder|ProcessVersionHistory make($value)
 * @method static Builder|ProcessVersionHistory create($value)
 * @property-read ProcessVersion $processVersion
 * @mixin Eloquent
 */
class ProcessVersionHistory extends Model {

    use HasUuids, HasFactory;

    protected $table = 'process_versions_history';

    protected $guarded = [];

    protected $casts = [
        'calculated' => 'array',
        'definition' => 'array',
        'command_payload' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function processVersion() {
        return $this->belongsTo(ProcessVersion::class, 'process_version_id', 'id');
    }

    /**
     * Makes the initial history item without any command.
     * @param ProcessVersion $processVersion
     * @return ProcessVersionHistory
     */
    public static function makeInitial(ProcessVersion $processVersion) {
        return self::makeWithCommand($processVersion);
    }

    /**
     * Gibt die Prozesstyp-Definition als Definition-Object zurück.
     * @return Attribute
     */
    protected function definition(): Attribute {
        return Attribute::make(get: fn(mixed $value) => new Definition($this->castAttribute('definition', $value)));
    }

    /**
     * Gibt die Prozesstyp-Definition als Array zurück.
     * @return array
     */
    public function getRawDefintion() {
        return $this->castAttribute('definition', $this->getRawOriginal('definition'));
    }

    /**
     * Makes the initial history item without any command.
     * @param ProcessVersion $processVersion
     * @param string|null $commandName
     * @param array|null $commandPayload
     * @return ProcessVersionHistory
     */
    public static function makeWithCommand(ProcessVersion $processVersion, string|null $commandName = null, array|null $commandPayload = null) {
        return self::make([
            'command' => $commandName,
            'command_payload' => $commandPayload,
            'calculated' => $processVersion->calculated,
            'definition' => $processVersion->definition->toArray()
        ]);
    }
}
