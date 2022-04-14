<?php

namespace Drupal\timeandzone\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Configure regional settings for this site.
 *
 * @internal
 */
class TimezoneForm extends ConfigFormBase {

  /**
   * The cache render service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheRender;

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Constructs a TimezoneForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheRender
   *   A cache backend interface instance.
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   The country manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, CacheBackendInterface $cacheRender, CountryManagerInterface $country_manager) {
    parent::__construct($config_factory);
    $this->cacheRender = $cacheRender;
    $this->countryManager = $country_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('cache.config'),
      $container->get('country_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'timeandzone_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['timeandzone.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('timeandzone.settings');
    $options_pages = $config->get('nfieldset');

    $form['#tree'] = TRUE;
    $form['nfieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Time Based on Zone'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['nfieldset']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country name'),
      '#default_value' => $options_pages[0]['country'] ?? '',
      '#empty_value' => '',
      '#empty_option' => t('- Select -'),
      '#options' => $this->countryManager->getList(),
      '#required' => TRUE,
      '#attributes' => ['class' => ['countryname-detect']],
    ];
    $form['nfieldset']['city']= [
      '#type' => 'textfield',
      '#title' => $this->t('City name'),
      '#required' => TRUE,
      '#default_value' => $options_pages[0]['city'] ?? '',
    ];
    $form['nfieldset']['zone'] = [
      '#type' => 'select',
      '#title' => $this->t('Timezone'),
      '#default_value' => $options_pages[0]['zone'] ?? '',
      '#empty_value' => '',
      '#empty_option' => t('- Select -'),
      '#options' => system_time_zones(NULL, TRUE),
      '#required' => TRUE,
      '#attributes' => ['class' => ['timezonename-detect']],
    ];


    return parent::buildForm($form, $form_state);
  }






  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $full_country_list = $this->countryManager->getList();
    $config = $this->config('timeandzone.settings');
    $values = $form_state->getValues();
    $opages = [];
    $all_options_pages = '';
    //foreach ($values['nfieldset'] as $key => $value) {
      $opages[0]['country'] = $values['nfieldset']['country'] ?? '';
      $opages[0]['city'] = $values['nfieldset']['city'] ?? '';
      $opages[0]['zone'] = $values['nfieldset']['zone'] ?? '';
      $opages[0]['date'] = 'M';
      //$options_pages[$ke  y]['time'] = $value['time'] ?: [];
      // Getting current time as per the selection of the zones
      // And convert in number of hours.
      date_default_timezone_set($values['nfieldset']['zone']);
      $time_division = explode(":", date('P'));
      if ($time_division[1] > 00) {
        $total_sum = $time_division[0] + ($time_division[1] / 60);
        $time_duration = substr(date('P'), 0, 1) . '' . $total_sum;
      }
      else {
        $time_duration = $time_division[0] + ($time_division[1] / 60);
      }
      $all_options_pages = $full_country_list[$values['nfieldset']['country']] . ',' . $values['nfieldset']['city'] . ',' . $time_duration . ',' . $values['nfieldset']['country'] . ',' . $opages[0]['date'] . '|';
      $config->set('nfieldset', $opages);  //var_dump($all_options_pages);die;
    //}
    $config->set('nfieldset_value', rtrim($all_options_pages, '|'));
    $config->save();
    $form_state->setRebuild(TRUE);
    return parent::submitForm($form, $form_state);
  }

}
