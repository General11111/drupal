<?php

namespace Drupal\ex_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class ExForm extends FormBase {


  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type'=>'textfield',
      '#title'=>$this->t('Ваше имя'),
      '#description'=>$this->t('Имя не должно содержать цифры'),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type'=>'textfield',
      '#title'=>$this->t('Ваша фамилия'),
      '#description'=>$this->t('Фамилия не должна содержать цифры'),
      '#required' => TRUE,
    ];

    $form['topic'] = [
      '#type'=>'textfield',
      '#title'=>$this->t('Тема сообщения'),
      '#description'=>$this->t('Введите тему'),
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type'=>'textarea',
      '#title'=>$this->t('Ваше сообщение'),
      '#description'=>$this->t('Введите своё сообщение'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type'=>'textfield',
      '#title'=>$this->t('Введите ваш e-mail'),
      '#description'=>$this->t('e-mail должен содержать .com'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Отправить форму'),
    ];

    return $form;
  }

  public function getFormId() {
    return 'ex_form_exform_form';
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

    $name = $form_state->getValue('name');
    $is_number = preg_match("/[\d]+/", $name, $match);

    if ($is_number > 0) {
      $form_state->setErrorByName('name', $this->t('Строка содержит цифру.'));
    }

    $last_name = $form_state->getValue('last_name');
    $is_number = preg_match("/[\d]+/", $last_name, $match);

    if ($is_number > 0) {
      $form_state->setErrorByName('last_name', $this->t('Строка содержит цифру.'));
    }

    $email = $form_state->getValue('email');
    if (strpos($email, '.com') === FALSE ) {
      $form_state->setErrorByName('email', $this->t('email не содержит .com.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    drupal_set_message(t('Почта: %email. отправлена', ['%email' => $email]));


    $topic = $form_state->getValue('topic');
    $message = $form_state->getValue('message');
    $res = mail('hello-1@general.hs-inbox.com', $topic, $message);

    if($res) {

      \Drupal::logger('ex-form')->notice('Mail is sent. E-mail: '.$form_state->getValue('email'));

      drupal_set_message('E-mail is sent!');

    }

    $email = $form_state->getValue('email');

    $url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."/?hapikey=6ea50274-acf6-4543-9670-c7e77187ecf8";

    $json = '{
  "properties": [
    {
      "property": "topic",
      "value": "Test"
    },
    {
      "property": "message",
      "value": "Test"
    }
  ]
}';

    $request = \Drupal::httpClient()->post($url, NULL, $json);

    try {
      $response = $request->send();
    }
    catch (\Guzzle\Http\Exception\RequestException $exception) {
      
    }


  }

}
