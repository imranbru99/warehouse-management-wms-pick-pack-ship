<?php

declare(strict_types=1);

it('returns a successful health response', function () {
    $this->get('/up')->assertSuccessful();
});

it('returns api health json', function () {
    $this->getJson('/api/v1/health')
        ->assertOk()
        ->assertJsonPath('ok', true);
});
