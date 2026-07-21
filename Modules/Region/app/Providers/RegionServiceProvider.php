<?php

namespace Modules\Region\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use Modules\Region\Repositories\Contracts\VillageRepositoryInterface;
use Modules\Region\Repositories\VillageRepository;

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
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // 
    public function register(): void
    {
        parent::register();

        $this->app->bind(\Modules\Region\Repositories\Contracts\VillageRepositoryInterface::class, \Modules\Region\Repositories\VillageRepository::class);
    }
}
