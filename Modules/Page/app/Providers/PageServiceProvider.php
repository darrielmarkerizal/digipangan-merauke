<?php

namespace Modules\Page\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Page\Repositories\Contracts\FaqRepositoryInterface;
use Modules\Page\Repositories\Contracts\PartnerRepositoryInterface;
use Modules\Page\Repositories\FaqRepository;
use Modules\Page\Repositories\PartnerRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class PageServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Page';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'page';

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
    // }

    public function register(): void
    {
        parent::register();

        $this->app->bind(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
    }
}
