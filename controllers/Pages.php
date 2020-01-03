<?php namespace Hackolein\Workflow\Pages;

use Backend\Widgets\Form;
use Carbon\Carbon;
use Cms\Classes\Page;
use October\Rain\Support\Traits\Singleton;

class Pages extends Backend\Classes\Pages
{

  use Singleton;

  public $implement = [];

  public function __construct()
  {
    parent::__construct();
  }

  const PUBLISHED_AT = 'published_at';
  const OFFLINE_AT   = 'offline_at';
  const STATUS       = 'status';
  const PUBLISHED    = 'published';
  const DRAFT        = 'draft';

  public function initSettings(Page $page)
  {
    $this->initDateSettings($page, self::PUBLISHED_AT);
    $this->initDateSettings($page, self::OFFLINE_AT);
    $this->initStatusSettings($page);
  }

  public function extendForm(Form $formWidget, $pageType)
  {
    $this->extendFormWidgetDate($formWidget, $pageType, self::PUBLISHED_AT);
    $this->extendFormWidgetDate($formWidget, $pageType, self::OFFLINE_AT);
    $this->extendFormWidgetStatus($formWidget, $pageType);
  }

  protected function initDateSettings(Page $page, $dateType)
  {
    $date = $page->apiBag['staticPage']->viewBag[$dateType] ?? $page->settings[$dateType] ?? null;

    if ($date === null) {
      $page->settings[$dateType] = $date;

      return $page;
    }

    try {
      $page->settings[$dateType] = Carbon::createFromFormat('Y-m-d H:i:s', $date);
    }
    catch (\InvalidArgumentException $e) {

      $page->settings[$dateType] = null;
    }

    return $page;
  }

  protected function initStatusSettings(Page $page)
  {
    $status = $page->apiBag['staticPage']->viewBag[self::STATUS] ?? $page->settings[self::STATUS] ?? null;

    $page->settings[self::STATUS] = $status;

    return $page;
  }

  /**
   * Extends the form with date fields.
   */
  protected function extendFormWidgetDate(Form $formWidget, $pageType, $dateType)
  {

    $fieldConfig = [
        'type'    => 'datepicker',
        'label'   => 'hackolein.workflow::lang.form.' . $dateType,
        'comment' => 'hackolein.workflow::lang.form.' . $dateType . '_comment',
        'span'    => 'left',
    ];

    switch ($pageType) {
      case 'Cms\Classes\Page':
        $formWidget->fields['settings[' . $dateType . ']'] = $fieldConfig;
        break;
      case 'RainLab\Pages\Classes\Page':
        $formWidget->fields['viewBag[' . $dateType . ']'] = $fieldConfig;
        break;
    }
  }

  /**
   * Extends the form with date fields.
   */
  protected function extendFormWidgetStatus(Form $formWidget, $pageType)
  {

    $fieldConfig = [
        'type'    => 'dropdown',
        'label'   => 'hackolein.workflow::lang.form.status.label',
        'comment' => 'hackolein.workflow::lang.form.status.comment',
        'options' => [
            self::DRAFT     => 'hackolein.workflow::lang.form.status.draft',
            self::PUBLISHED => 'hackolein.workflow::lang.form.status.published',
        ],
        'span'    => 'right',
    ];

    switch ($pageType) {
      case 'Cms\Classes\Page':
        $formWidget->fields['settings[' . self::STATUS . ']'] = $fieldConfig;
        break;
      case 'RainLab\Pages\Classes\Page':
        $formWidget->fields['viewBag[' . self::STATUS . ']'] = $fieldConfig;
        break;
    }
  }
}
