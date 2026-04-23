<?php

namespace App\Observers;

use App\Models\User;
use Jdenticon\Identicon;

/**
 * Class UserObserver
 * @package App\Observers
 */
class UserObserver {

    /**
     * Identicon is created based on user id and saved at the "image" attribute.
     * @param User $user
     * @return void
     */
    public function created(User $user) {
        $icon = new Identicon();
        $icon->setValue($user->id);
        $icon->setSize(250);
        $user->update(['image' => $icon->getImageDataUri('svg')]);
    }

}
