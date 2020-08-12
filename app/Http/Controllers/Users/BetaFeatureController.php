<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

/**
 * Beta 功能.
 */
class BetaFeatureController
{
    /**
     * Return a list of beta features available to a user.
     *
     * @throws \Exception
     */
    @@\Route('get', 'api/user/beta_features')
    public function __invoke(): void
    {
        JWTController::getUser();
    }

    /**
     * update a user's beta_feature.
     *
     * <pre>
     * {"beta_feature.enabled":true}
     * <pre>
     *
     * @throws \Exception
     */
    @@\Route('patch', 'api/user/beta_feature/{beta_feature_id}')
    public function enable(): void
    {
        JWTController::getUser();
    }

    /**
     * delete a user's beta feature.
     *
     * @param array $args
     *
     * @throws \Exception
     */
    @@\Route('delete','api/user/beta_feature/{beta_feature_id}')
    public function delete(...$args): void
    {
        JWTController::getUser();
    }
}
