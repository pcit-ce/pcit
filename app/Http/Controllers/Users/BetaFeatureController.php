<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use PCIT\Framework\Attributes\Route;

/**
 * Beta 功能.
 */
class BetaFeatureController
{
    /**
     * Return a list of beta features available to a user.
     */
    #[Route('get', 'api/user/beta_features')]
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
     */
    #[Route('patch', 'api/user/beta_feature/{beta_feature_id}')]
    public function enable(): void
    {
        JWTController::getUser();
    }

    /**
     * delete a user's beta feature.
     *
     * @param array $args
     */
    #[Route('delete','api/user/beta_feature/{beta_feature_id}')]
    public function delete(...$args): void
    {
        JWTController::getUser();
    }
}
