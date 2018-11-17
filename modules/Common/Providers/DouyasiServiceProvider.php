<?php

namespace Modules\Common\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Common\Extensions\DouyasiValidator;

/**
 * DouyasiValidator 扩展自定义验证类 服务提供者
 *
 * @author raoyc<raoyc2009@gmail.com>
 */
class DouyasiServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        /*注册自定义验证类*/
        $this->app['validator']->resolver(function ($translator, $data, $rules, $messages) {
            return new DouyasiValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
