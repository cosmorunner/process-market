<?php

namespace App\Http\Controllers;


use Illuminate\Contracts\Support\Renderable;

/**
 * Class LegalController
 * @package App\Http\Controllers
 */
class LegalController extends Controller {

    /**
     * Show the home page
     * @param string|null $section
     * @return Renderable
     */
    public function index(?string $section = 'terms') {
        if (!in_array($section, ['terms', 'privacy', 'imprint', 'licenses'])) {
            $section = 'terms';
        }

        $title = match ($section) {
            'terms' => 'AGBs',
            'privacy' => 'Datenschutzerklärung',
            'imprint' => 'Impressum',
            'licenses' => 'Lizenzen'
        };

        return view('legal', [
            'section' => $section,
            'title' => $title
        ]);
    }
}
