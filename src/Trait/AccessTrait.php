<?php

namespace Vision\BuilderEngine\Trait;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait AccessTrait
{
    public function isGranted(array $roles): void
    {
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (!$this->authorizationChecker->isGranted($role)) {
                    throw new AccessDeniedException();
                }
            }
        }
    }
}
