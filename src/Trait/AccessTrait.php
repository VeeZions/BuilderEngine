<?php

namespace VeeZions\BuilderEngine\Trait;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait AccessTrait
{
    /**
     * @param array<int, string> $roles
     */
    public function isGranted(array $roles, bool $throwable = true): bool
    {
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($this->authorizationChecker->isGranted($role)) {
                    return true;
                }
            }

            if (true === $throwable) {
                throw new AccessDeniedException();
            }

            return false;
        }

        return true;
    }
}
