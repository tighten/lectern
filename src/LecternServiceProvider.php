<?php

namespace Tightenco\Lectern;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Tightenco\Lectern\Console\Commands\InstallCommand;
use Tightenco\Lectern\Http\Middleware\LecternBanCheck;
use Tightenco\Lectern\Models\Ban;
use Tightenco\Lectern\Models\Category;
use Tightenco\Lectern\Models\Mention;
use Tightenco\Lectern\Models\Post;
use Tightenco\Lectern\Models\Reaction;
use Tightenco\Lectern\Models\Subscription;
use Tightenco\Lectern\Models\Thread;
use Tightenco\Lectern\Policies\CategoryPolicy;
use Tightenco\Lectern\Policies\PostPolicy;
use Tightenco\Lectern\Policies\ThreadPolicy;

class LecternServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/lectern.php',
            'lectern'
        );
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerRoutes();
        $this->registerPolicies();
        $this->registerMiddleware();
        $this->registerMorphMaps();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    protected function registerPublishing(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/lectern.php' => config_path('lectern.php'),
        ], 'lectern-config');
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Thread::class, ThreadPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('lectern.ban', LecternBanCheck::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
        ]);
    }

    protected function registerMorphMaps(): void
    {
        Relation::morphMap([
            'lectern_ban' => Ban::class,
            'lectern_category' => Category::class,
            'lectern_mention' => Mention::class,
            'lectern_post' => Post::class,
            'lectern_reaction' => Reaction::class,
            'lectern_subscription' => Subscription::class,
            'lectern_thread' => Thread::class,
        ]);
    }
}
