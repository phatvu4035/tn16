<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;
use Collective\Html\HtmlFacade as Html;

class HtmlComponentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('openForm', 'components.form.openForm', ['title' => 'New Form', 'formParam' => []]);
        Form::component('closeForm', 'components.form.closeForm', []);
        Form::component('inputField', 'components.form.input', [
            'options' => [
                    'label' => '', 
                    'smallMessage' => '',
                    'placeHolder' => '', 
                    'name' => '', 
                    'type' => '',
                    'value' => '',
                    'options' => [],
                    'cloneable' => false
                ]
            ]);
        Form::component('dropDown', 'components.form.dropDown', [
            'options' => [
                    'label' => '', 
                    'name' => '', 
                    'data' => [],
                    'options' => [],
                    'cloneable' => false
                ]
            ]);
        Form::component('inputRadio', 'components.form.radio', [
            'options' => [
                'name' => '', 
                'data' => [],
                'label'=> '',
                'class'=> ''
            ]
        ]);

        Form::component('inputTextarea', 'components.form.textarea', [
            'options' => [
                    'label' => '', 
                    'placeHolder' => '', 
                    'name' => '', 
                    'type' => '',
                    'value' => '',
                    'options' => [],
                    'cloneable' => false
                ]
            ]);

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
