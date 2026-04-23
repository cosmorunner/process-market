<?php

namespace App\Observers;

use App\Models\Access;
use App\Models\Organisation;
use App\Models\User;
use App\Utils\RedisHelper;

/**
 * Class AccessObserver
 * @package App\Observers
 */
class AccessObserver {

    /**
     * @param Access $access
     * @return void
     */
    public function created(Access $access) {
        if($access->resource instanceof Organisation && $access->recipient instanceof User) {
            RedisHelper::flush($access->recipient);
        }
    }

    /**
     * @param Access $access
     * @return void
     */
    public function deleted(Access $access) {
        // Reset the user contexts to NULL. "resource_id" references the id of the organisation.
        User::whereContext($access->resource_id)->update(['context' => null]);
    }

    /**
     * @param Access $access
     * @return void
     */
    public function deleting(Access $access) {
        if($access->resource instanceof Organisation && $access->recipient instanceof User) {
            RedisHelper::flush($access->recipient);
        }
    }

}
