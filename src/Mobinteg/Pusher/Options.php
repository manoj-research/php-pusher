<?php

namespace Mobinteg;


class Options {
  public $gcmKey = null;
  public $apnsCertificatePath = null;
  public $apnsPassword = null;

  /**
   * Options constructor.
   * @param $gcmKey string
   * @param $apnsCertificatePath string
   * @param null $apnsPassword string
   * @param int $apnsProduction int
   */
  public function __construct ( $gcmKey, $apnsCertificatePath, $apnsPassword = null, $apnsProduction = 0 ) {
    $this->gcmKey = $gcmKey;
    $this->apnsCertificatePath = $apnsCertificatePath;
    $this->apnsPassword = $apnsPassword;
    $this->apnsProduction = $apnsProduction;
  }
}