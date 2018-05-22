<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\APITokenController;

class BetaFeatureController
{
    /**
     * /user/{git_type}/{username}/beta_features.
     *
     * Return a list of beta features available to a user.
     *
     * @param array $args
     */
    public function list(...$args): void
    {
        list($git_type, $user_name) = $args;

        APITokenController::checkByUser(...$args);
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
     * /user/{git_type}/{username}/beta_feature/{beta_feature_id}
     *
     * @param array $args
     */
    public function enable(...$args): void
    {
        list($git_type, $username, $beta_feature_id) = $args;

        APITokenController::checkByUser($git_type, $username);
    }

    /**
     * delete a user's beta feature.
     *
     * delete
     *
     * /user/{git_type}/{username}/beta_feature/{beta_feature_id}
     *
     * @param array $args
     */
    public function delete(...$args): void
    {
        list($git_type, $username, $beta_feature_id) = $args;

        APITokenController::checkByUser($git_type, $username);
    }
}
