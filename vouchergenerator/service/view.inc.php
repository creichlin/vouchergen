<?php

namespace view;

require_once('include/Twig/Autoloader.php');


class View {

  private $i18n;
  private $twig;
  private $model = [];

  function __construct() {
    // load the twig stuff
    \Twig_Autoloader::register();

    $twigLoader = new \Twig_Loader_Filesystem('templates');
    $this->twig = new \Twig_Environment($twigLoader, array());



    $filter = new \Twig_SimpleFilter('i18n', function ($string) {
      return $this->i18n->get($string);
    });

    $this->twig->addFilter($filter);

    $this->model['messages'] = [];

    $this->i18n = new \aLang("view", "en");
  }

  function setTitle($title) {
    $this->set('title', $title);
  }

  private function addMessage($type, $key, $model = []) {
    $message = $this->i18n->get($key);

    // micro template engine
    if (preg_match_all("/{{(.*?)}}/", $message, $m)) {
      foreach ($m[1] as $i => $varname) {
        $message = str_replace($m[0][$i], sprintf('%s', $model[$varname]), $message);
      }
    }

    $this->model['messages'][] = [
      'type' => 'warning',
      'key' => $key,
      'message' => $message
    ];
  }

  function addWarning($key, $model = []) {
    $this->addMessage("warning", $key, $model);
  }

  function addInfo($key, $model = []) {
    $this->addMessage("info", $key, $model);
  }

  function set($key, $value) {
    $this->model[$key] = $value;
  }

  function render($template) {
    print($this->twig->render($template, $this->model));
  }

  function setLoggedIn() {
    $this->set("loggedIn", True);
  }
}


?>