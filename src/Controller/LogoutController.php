<?php

declare(strict_types = 1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
    }
}
