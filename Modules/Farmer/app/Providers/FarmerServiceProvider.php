<?php

namespace Modules\Farmer\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Farmer\Repositories\CommodityRepository;
use Modules\Farmer\Repositories\Contracts\CommodityRepositoryInterface;
use Modules\Farmer\Repositories\Contracts\FarmerGroupRepositoryInterface;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\Farmer\Repositories\FarmerGroupRepository;
use Modules\Farmer\Repositories\FarmerRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class FarmerServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Farmer';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'farmer';

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

        $this->app->bind(FarmerGroupRepositoryInterface::class, FarmerGroupRepository::class);
        $this->app->bind(CommodityRepositoryInterface::class, CommodityRepository::class);
        $this->app->bind(FarmerRepositoryInterface::class, FarmerRepository::class);
    }
}
