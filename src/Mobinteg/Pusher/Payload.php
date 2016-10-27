<?php


namespace Mobinteg;


class Payload {

  /**
   * @param $title string
   * @param null $body string
   * @param null $type string
   * @param null $data array
   * @param null $badge int
   */
  public function __construct ( $title, $body = null, $type = null, $data = null, $badge = null ) {
    $this->title = $title;
    $this->body = $body;
    $this->type = $type;
    $this->data = $data;
    $this->badge = $badge;
  }
}