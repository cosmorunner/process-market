<?php

namespace App\Observers;

use App\Interfaces\Cachable;
use App\Models\License;

/**
 * Class LicenseObserver
 * @package App\Observers
 */
class LicenseObserver {

    /**
     * @param License $license
     * @return void
     */
    public function created(License $license) {
        if($license->owner instanceof Cachable) {
            $license->owner->flushCache();
        }
    }

}
