<?php

namespace Mobinteg;

require_once '../../vendor/autoload.php';

use PHP_GCM\Message;
use PHP_GCM\Sender;

class Pusher {

  public $options = null;
  private $gcmConnection = null;
  private $apnsConnection = null;

  public function __construct ( Options $options ) {
    $this->options = $options;
  }

  public function send ( $devices, Payload $payload ) {
    return [
      "android" => $this->sendAndroid( byPlatform( $devices, "android" ), $payload ),
      "ios" => $this->sendIos( byPlatform( $devices, "ios" ), $payload ),
    ];
  }

  /**
   * @param $tokens string[]
   * @param Payload $payload
   */
  public function sendIos ( $tokens, Payload $payload ) {
    $connection = $this->getApnsConnection();
    $message = new \ApnsPHP_Message();
    foreach ( $tokens as $token ) {
      $message->addRecipient( $token );
    }
    $message->setBadge( $payload->badge );
    $message->setText( $payload->title );
    if ( isset( $payload->type ) ) {
      $message->setCustomProperty( "type", $payload->type );
    }
    if ( isset( $payload->data ) ) {
      $message->setCustomProperty( "data", $payload->data );
    }
    $connection->add( $message );

    return $connection->send();
  }

  /**
   * @param $tokens string[]
   * @param Payload $payload
   * @return \PHP_GCM\MulticastResult
   */
  public function sendAndroid ( $tokens, Payload $payload ) {
    $connection = $this->getGcmConnection();

    $message = new Message( $payload[ "collapseKey" ], [
      "title" => $payload->title,
      "message" => $payload->body,
      "type" => $payload->type,
      "data" => $payload->data,
      "badge" => $payload->badge,
    ] );

    return $connection->sendMulti( $message, $tokens, 10 );
  }


  private function getGcmConnection () {
    if ( !$this->gcmConnection ) {
      $this->gcmConnection = new Sender( $this->options->gcmKey );
    }

    return $this->gcmConnection;
  }

  private function getApnsConnection () {
    if ( !$this->apnsConnection ) {
      $connection = new \ApnsPHP_Push( $this->options->apnsProduction, $this->options->apnsCertificatePath );
      $connection->connect();
      $this->apnsConnection = $connection;
    }

    return $this->apnsConnection;
  }
}

function byPlatform ( $devices, $platform ) {
  $platformDevices = array_values( array_filter( $devices, function ( $device ) use ( $platform ) {
    return $device[ "platform" ] === $platform;
  } ) );

  return array_map( function ( $device ) {
    return $device[ "token" ];
  }, $platformDevices );
}