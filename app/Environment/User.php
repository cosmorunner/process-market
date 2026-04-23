<?php /** @noinspection PhpUnused */

namespace App\Environment;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Blueprint-Repräsentation eines System-Benutzers. Wird für die Environmenterzeugung genutzt.
 * Class User
 * @package App\Environment
 */
class User extends AbstractModel {

    public string $id;
    public string|null $identity_process_type;
    public string|null $identity_process_instance;
    public string $first_name;
    public string $last_name;
    public string $locale;
    public string $email;
    public string $email_verified_at;
    public string $account_completed_at;
    public string $password;
    public string $remember_token;
    public array $aliases = [];
    public array $tags = [];

    /**
     * Erzeugt ein neues Objekt für eine Blueprint-Repräsentation.
     * @param array $options
     * @return User
     */
    public static function make(array $options = []) {
        $id = $options['id'] ?? Uuid::uuid4()->toString();
        $firstName = $options['first_name'] ?? 'Demo';
        $lastName = $options['last_name'] ?? 'User';

        return new self([
            'id' => $id,
            'identity_process_type' => $options['identity_process_type'] ?? '',
            'identity_process_instance' => $options['identity_process_instance'] ?? '',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'aliases' => $options['aliases'] ?? [],
            'tags' => $options['tags'] ?? [],
            'locale' => 'de',
            'email' => $options['email'] ?? Str::snake(substr($firstName, 0, 2)) . '.' . Str::snake(substr($lastName, 0, 8)) . '@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => '$2y$10$KF8Jr70O7oMmH1CejqZ2TutmcLFeAu/YhDdhVKO.ad8C/kXoJSbjm',
            'remember_token' => Str::random(10),
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'identity_process_type' => $this->identity_process_type ?? '',
            'identity_process_instance' => $this->identity_process_instance ?? '',
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'aliases' => $this->aliases,
            'tags' => $this->tags,
            'locale' => $this->locale,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'remember_token' => $this->remember_token
        ];
    }
}
