<?php

namespace Mobinteg\Pusher;


class Options {
  public $gcmKey = null;
  public $apnsCertificatePath = null;
  public $apnsPassword = null;

  /**
   * Options constructor.
   * @param $gcmKey string
   * @param $apnsCertificatePath string
   * @param null $apnsPassword string
   * @param int $apnsProduction 0
   * @param bool $oldGcmMode false
   */
  public function __construct ( $gcmKey, $apnsCertificatePath, $apnsPassword = null, $apnsProduction = 0, $oldGcmMode = false ) {
    $this->gcmKey = $gcmKey;
    $this->apnsCertificatePath = $apnsCertificatePath;
    $this->apnsPassword = $apnsPassword;
    $this->apnsProduction = $apnsProduction;
    $this->oldGcmMode = $oldGcmMode;
  }
}