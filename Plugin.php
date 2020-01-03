<?php namespace Hackolein\Workflow;

use Backend\Facades\BackendAuth;
use Backend\Widgets\Form;
use Carbon\Carbon;
use Cms\Classes\Page;
use Hackolein\Workflow\Pages\Pages;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use Illuminate\Support\Facades\Event;

class Plugin extends PluginBase
{

  public function boot()
  {
    // Extend all backend form usage
    Event::listen(
        'backend.form.extendFieldsBefore', function (Form $formWidget) {

      if ($formWidget->model instanceof Page) {

        Pages::instance()->extendForm($formWidget, Page::class);
      }


      if (PluginManager::instance()->exists('RainLab.Pages') && $formWidget->model instanceof \RainLab\Pages\Classes\Page) {

        Pages::instance()->extendForm($formWidget, \RainLab\Pages\Classes\Page::class);
      }

    }
    );

    Event::listen(
        'cms.page.start', function (\Cms\Classes\Pages $controller) {

      Pages::instance()->initSettings($controller->getPage());

      $page = $controller->getPage();
      $now = Carbon::now();

      if (!BackendAuth::getUser()
          &&
          ($page->settings[Pages::STATUS] == null || $page->settings[Pages::STATUS] == Pages::PUBLISHED)) {
        if ($page->settings[Pages::PUBLISHED_AT] && $now->lt($page->settings[Pages::PUBLISHED_AT])) {

          return $controller->run(404);
        }

        if ($page->settings[Pages::OFFLINE_AT] && $now->gt($page->settings[Pages::OFFLINE_AT])) {

          return $controller->run(404);
        }
      }

      if ($page->settings[Pages::STATUS] == Pages::DRAFT && !BackendAuth::getUser()) {

        return $controller->run(404);
      }


    }
    );
  }
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }
}
