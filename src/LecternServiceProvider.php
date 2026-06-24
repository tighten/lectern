<?php

namespace Tighten\Lectern;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Tighten\Lectern\Console\Commands\InstallCommand;
use Tighten\Lectern\Http\Middleware\LecternBanCheck;
use Tighten\Lectern\Models\Ban;
use Tighten\Lectern\Models\Category;
use Tighten\Lectern\Models\Mention;
use Tighten\Lectern\Models\Post;
use Tighten\Lectern\Models\Reaction;
use Tighten\Lectern\Models\Subscription;
use Tighten\Lectern\Models\Thread;
use Tighten\Lectern\Policies\CategoryPolicy;
use Tighten\Lectern\Policies\PostPolicy;
use Tighten\Lectern\Policies\ThreadPolicy;

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
