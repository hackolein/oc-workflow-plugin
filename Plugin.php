<?php namespace Hackolein\Workflow;

use Backend\Facades\BackendAuth;
use Backend\Widgets\Form;
use Carbon\Carbon;
use Cms\Classes\Page;
use Hackolein\ContentSynchronisation\classes\Controller;
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

        Controller::instance()->extendForm($formWidget, Page::class);
      }


      if (PluginManager::instance()->exists('RainLab.Pages') && $formWidget->model instanceof \RainLab\Pages\Classes\Page) {

        Controller::instance()->extendForm($formWidget, \RainLab\Pages\Classes\Page::class);
      }

    }
    );

    Event::listen(
        'cms.page.start', function (\Cms\Classes\Controller $controller) {

      Controller::instance()->initSettings($controller->getPage());

      $page = $controller->getPage();
      $now = Carbon::now();

      if (!BackendAuth::getUser()
          &&
          ($page->settings[Controller::STATUS] == null || $page->settings[Controller::STATUS] == Controller::PUBLISHED)) {
        if ($page->settings[Controller::PUBLISHED_AT] && $now->lt($page->settings[Controller::PUBLISHED_AT])) {

          return $controller->run(404);
        }

        if ($page->settings[Controller::OFFLINE_AT] && $now->gt($page->settings[Controller::OFFLINE_AT])) {

          return $controller->run(404);
        }
      }

      if ($page->settings[Controller::STATUS] == Controller::DRAFT && !BackendAuth::getUser()) {

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
