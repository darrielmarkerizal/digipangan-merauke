<?php

namespace Modules\Product\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use Modules\Product\Repositories\Contracts\UnitRepositoryInterface;
use Modules\Product\Repositories\UnitRepository;
use Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Modules\Product\Repositories\ProductCategoryRepository;

class ProductServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Product';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'product';

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

        $this->app->bind(\Modules\Product\Repositories\Contracts\UnitRepositoryInterface::class, \Modules\Product\Repositories\UnitRepository::class);
        $this->app->bind(\Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface::class, \Modules\Product\Repositories\ProductCategoryRepository::class);
    }
}
