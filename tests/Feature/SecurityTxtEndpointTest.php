<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SecurityTxtEndpointTest extends TestCase
{
    public function test_security_txt_endpoint_returns_ok_with_rfc_directives(): void
    {
        $response = $this->get('/.well-known/security.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('Contact: mailto:keamanan-informasi@magkom.go.id');
        $response->assertSee('Expires: 2027-12-31T23:59:59.000Z');
        $response->assertSee('Preferred-Languages: id, en');
    }
}
