<?php

use Illuminate\Support\Facades\Schema;

it('runs the install command', function () {
    $this->artisan('filament-workflows:install')
        ->expectsOutputToContain('Filament workflows installed successfully.')
        ->assertSuccessful();

    expect(Schema::hasTable('workflows'))->toBeTrue()
        ->and(Schema::hasTable('triggers_has_actions'))->toBeTrue();
});
