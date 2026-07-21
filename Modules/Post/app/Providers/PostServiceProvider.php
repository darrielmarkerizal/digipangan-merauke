<?php

namespace Modules\Post\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;
use Modules\Post\Repositories\PostCategoryRepository;

class PostServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Post';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'post';

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

        $this->app->bind(\Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface::class, \Modules\Post\Repositories\PostCategoryRepository::class);
    }
}
