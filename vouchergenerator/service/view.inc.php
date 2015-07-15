<?php

namespace view;

require_once('include/Twig/Autoloader.php');


class View {

  private $twig;
  private $model = [];

  function __construct() {
    // load the twig stuff
    \Twig_Autoloader::register();

    $twigLoader = new \Twig_Loader_Filesystem('templates');
    $this->twig = new \Twig_Environment($twigLoader, array());

    $this->model['messages'] = [];
  }

  function setTitle($title) {
    $this->set('title', $title);
  }

  function addWarning($key) {
    $this->model['messages'][] = [
      'type' => 'warning',
      'key' => $key,
      'message' => $key
    ];
  }

  function addInfo($key) {
    $this->model['messages'][] = [
      'type' => 'info',
      'key' => $key,
      'message' => $key
    ];
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