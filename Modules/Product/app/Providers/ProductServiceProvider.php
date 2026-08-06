<?php

namespace Modules\Product\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Product\Console\PurgeProductInteractionsCommand;
use Modules\Product\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductInteractionRepositoryInterface;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Product\Repositories\Contracts\UnitRepositoryInterface;
use Modules\Product\Repositories\ProductCategoryRepository;
use Modules\Product\Repositories\ProductInteractionRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\UnitRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

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
    protected array $commands = [
        PurgeProductInteractionsCommand::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductInteractionRepositoryInterface::class, ProductInteractionRepository::class);
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('interactions:purge')->daily();
    }
}
