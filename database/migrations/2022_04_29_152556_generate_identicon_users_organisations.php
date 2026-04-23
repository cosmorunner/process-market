<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use App\Models\Organisation;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        foreach (User::all() as $user) {
            $icon = new \Jdenticon\Identicon();
            $icon->setValue($user->id);
            $icon->setSize(250);
            $user->update(['image' => $icon->getImageDataUri('svg')]);
        }

        foreach (Organisation::all() as $orgnisation) {
            $icon = new \Jdenticon\Identicon();
            $icon->setValue($orgnisation->id);
            $icon->setSize(250);
            $orgnisation->update(['image' => $icon->getImageDataUri('svg')]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        User::query()->update(['image' => null]);
        Organisation::query()->update(['image' => null]);
    }
};
