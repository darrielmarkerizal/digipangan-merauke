<?php

namespace Modules\Region\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Region\Repositories\Contracts\RegionRepositoryInterface;
use Modules\Region\Repositories\Contracts\VillageRepositoryInterface;
use Modules\Region\Repositories\RegionRepository;
use Modules\Region\Repositories\VillageRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class RegionServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Region';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'region';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    //
    public function register(): void
    {
        parent::register();

        $this->app->bind(VillageRepositoryInterface::class, VillageRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
    }
}
