<?php

namespace VeeZions\BuilderEngine\Trait;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait AccessTrait
{
    public function isGranted(array $roles, bool $throwable = true): bool
    {
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($this->authorizationChecker->isGranted($role)) {
                    return true;
                }
            }

            if ($throwable === true) {
                throw new AccessDeniedException();
            }

            return false;
        }

        return true;
    }
}
