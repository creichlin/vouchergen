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
  }

  function setTitle($title) {
    $this->set('title', $title);
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