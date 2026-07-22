<?php

use Illuminate\Console\Scheduling\Schedule;

it('menjadwalkan pencadangan harian basis data dan media (NFR-17)', function () {
    $commands = collect(app(Schedule::class)->events())->map(fn ($event) => $event->command);

    expect($commands->contains(fn ($command) => str_contains((string) $command, 'backup:run')))->toBeTrue()
        ->and($commands->contains(fn ($command) => str_contains((string) $command, 'backup:clean')))->toBeTrue();
});
