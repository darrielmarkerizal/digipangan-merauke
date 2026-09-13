<?php

namespace Modules\Post\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;
use Modules\Post\Repositories\PostCategoryRepository;
use Modules\Post\Repositories\PostRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

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
     * @param  $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    //
    public function register(): void
    {
        parent::register();

        $this->app->bind(PostCategoryRepositoryInterface::class, PostCategoryRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }
}
