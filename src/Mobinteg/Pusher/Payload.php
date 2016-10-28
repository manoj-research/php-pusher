<?php


namespace Mobinteg\Pusher;


class Payload {

  /**
   * @param $title string
   * @param null $body string
   * @param null $type string
   * @param null $data array
   * @param null $badge int
   * @param null $sound string
   * @param null $expiry int
   */
  public function __construct ( $title, $body = null, $type = null, $data = null, $badge = null, $sound = null, $expiry = null) {
    $this->title = $title;
    $this->body = $body;
    $this->type = $type;
    $this->data = $data;
    $this->badge = $badge;
    $this->sound = $sound;
    $this->expiry = $expiry;
  }
}