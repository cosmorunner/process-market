<?php /** @noinspection PhpUnused */

namespace App\Http\Controllers\Api;

use App\ConsoleConnector;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAllisaPlatform;
use App\Models\System;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class IndexController
 * @package App\Http\Controllers\Api
 */
class IndexController extends Controller {

    /**
     * Erstellt eine Allisa Demo Plattform.
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function storeAllisaPlatform(StoreAllisaPlatform $request) {
        $validated = $request->validated();

        // Allisa Plattform bei der Allisa Console erstellen.
        $connector = new ConsoleConnector();
        $platform = $connector->createPlatform([
            'identifier' => $validated['identifier'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name']
        ]);

        $user = Auth::user();
        $url = $platform['url'];
        $name = $platform['name'];
        $clientId = $platform['allisa_market_client']['client_id'];
        $clientSecret = $platform['allisa_market_client']['client_secret'];

        // Allisa Plattform in der Prozessfabrik registrieren.
        System::register($url, $name, $user, $clientId, $clientSecret);

        // Beim Benutzer speichern, dass dieser die Demo initialisiert hat.
        $user->update([
            'demo_identifier' => $validated['identifier']
        ]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Prüft, ob es bereits eine Allisa Plattform mit dem Identifier gibt.
     * @param string $identfier
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function allisaPlatformExists(string $identfier) {
        if (!preg_match('/^[a-z][a-z\d-]+$/', $identfier) || strlen($identfier) > 30) {
            return response()->json(['errors' => [
                'identifier' => ['Nur a-z, 0-9 und Bindestrich. Mit Buchstaben beginnen. Max. 30 Zeichen.']
            ]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $connector = new ConsoleConnector();

        return response()->json(['exists' => $connector->platformExists($identfier)]);
    }
}
