<?php


namespace Mobinteg\Pusher;


class Device {
  /**
   * @param $token string
   * @param $platform string
   */
  public function __construct ( $token, $platform ) {
    $this->token = $token;
    $this->platform = $platform;
  }
}