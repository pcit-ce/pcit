<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\APITokenController;

class BetaFeatureController
{
    /**
     * /user/beta_features.
     *
     * Return a list of beta features available to a user.
     *
     * @throws \Exception
     */
    public function __invoke(): void
    {
        APITokenController::getUser();
    }

    /**
     * update a user's beta_feature.
     *
     * patch
     *
     * <pre>
     * {"beta_feature.enabled":true}
     * <pre>
     *
     * /user/beta_feature/{beta_feature_id}
     *
     * @throws \Exception
     */
    public function enable(): void
    {
        APITokenController::getUser();
    }

    /**
     * delete a user's beta feature.
     *
     * delete
     *
     * /user/beta_feature/{beta_feature_id}
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function delete(...$args): void
    {
        APITokenController::getUser();
    }
}
