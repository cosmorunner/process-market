<?php /** @noinspection PhpParamsInspection */

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Ramsey\Uuid\Uuid;

/**
 * Class ClientsTableSeeder
 */
class ClientsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     * @return void
     */
    public function run() {
        $blueprint = empty(func_get_args()) ? config('blueprints.default') : func_get_args()[0];
        $clientRepository = new ClientRepository();

        foreach (Arr::get($blueprint, 'clients', []) as $type => $clientOptions) {
            $name = $clientOptions['name'] ?? null;
            $redirect = $clientOptions['redirect'] ?? config('app.url');

            switch ($type ?? null) {
                case 'personal':
                    $clientRepository->createPersonalAccessClient(null, $name, $redirect);

                    // Beim Personal Acces Client wird die Client-id und Secret aus der .env-Datei gesetzt.
                    $clientModel = Client::firstWhere('name', '=', $name);
                    $clientModel?->update([
                        'id' => config('passport.personal_access_client.id') ?? Uuid::uuid4()->toString(),
                        'secret' => config('passport.personal_access_client.secret') ?? Str::random(40)
                    ]);

                    break;
                case 'password':
                    $clientRepository->createPasswordGrantClient(null, $name, $redirect);

                    // Beim "simulation" und "live-demo"-Blueprint immer jene Id und Secret aus dem Blueprint nehmen,
                    // sodass bei jedem simulation.sqlite Update diese Werte nicht ändern und bei der Prozessfabrik
                    // nicht ständig die Environment-Werte geändert werden müssen.
                    if (in_array(Arr::get($blueprint, 'name'), ['simulation', 'live-demo'])) {
                        $clientModel = Client::firstWhere('name', '=', $name);
                        $clientModel?->update([
                            'id' => $clientOptions['id'] ?? 'cc2efc67-61e5-4cf5-bfd4-5b3a13079eae',
                            'secret' => $clientOptions['secret'] ?? '91e93cfb-739f-4c18-bc68-cd607c98d395'
                        ]);
                        $clientModel->refresh();
                    }

                    break;
                case 'client_credentials';
                    $clientRepository->create(null, $name, $redirect);
                    break;
            }
        }
    }
}
