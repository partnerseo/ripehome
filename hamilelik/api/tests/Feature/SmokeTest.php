<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    #[Test]
    public function the_root_url_leads_to_the_panel(): void
    {
        // Bu uygulamanın herkese açık bir web sayfası yok: kök adres panele gider.
        $this->get('/')->assertRedirect('/admin');
    }

    #[Test]
    public function the_health_check_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
