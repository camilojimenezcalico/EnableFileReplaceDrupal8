<?php

namespace Drupal\EnableFileReplace\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Masterminds\HTML5\Exception;

/**
 * Class EnableFileReplaceForm.
 */
class EnableFileReplaceForm extends FormBase
{


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'enable_file_replace_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['change_default_configuration_for'] = [
      '#type' => 'radios',
      '#title' => $this->t('Change Default Configuration For Drupal Saved Files'),
      '#options' => [1 => $this->t('Yes'), 0 => $this->t('no')],
      '#default_value' => 0,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $path = DRUPAL_ROOT . '/core/includes/file.inc';
    $lines = file($path);
    $search = 'const FILE_EXISTS_RENAME = ';
    $result = '';
    foreach ($form_state->getValues() as $key => $value) {
      if ($key === 'change_default_configuration_for') {
        foreach ($lines as $line) {
          if (strpos($line, $search) !== FALSE) {
            if ($value) {
              $result .= 'const FILE_EXISTS_RENAME = 1;' . PHP_EOL;
            }
            else {
              $result .= 'const FILE_EXISTS_RENAME = 0;' . PHP_EOL;
            }
          }
          else {
            $result .= $line;
          }
        }
        try {

          file_put_contents($path, $result);
        }
        catch (Exception $e) {
          drupal_set_message($e->getMessage());
        }
        drupal_flush_all_caches();
        drupal_set_message(t('Success'));
      }
    }

  }

}
