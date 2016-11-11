<?php

namespace Mobinteg\Pusher;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use PHP_GCM\Sender;

class Pusher {

  public $options = null;
  private $gcmConnection = null;
  private $apnsConnection = null;

  public function __construct ( Options $options ) {
    $this->options = $options;
  }

  /**
   * @param Device[] $devices
   * @param Payload $payload
   * @return array
   */
  public function send ( $devices, Payload $payload ) {
    $androidDevices = byPlatform( $devices, "android" );
    $iosDevices = byPlatform( $devices, "ios" );

    return [
      "android" => $androidDevices ? $this->sendAndroid( $androidDevices, $payload ) : null,
      "ios" => $iosDevices ? $this->sendIos( $iosDevices, $payload ) : null,
    ];
  }

  /**
   * @param string[] $tokens
   * @param Payload $payload
   */
  public function sendIos ( $tokens, Payload $payload ) {
    $connection = $this->getApnsConnection();
    $message = new \ApnsPHP_Message();
    foreach ( $tokens as $token ) {
      $message->addRecipient( $token );
    }
    if (isset($payload->title)) {
      $message->setText( $payload->title );
    }
    if (isset($payload->badge)) {
      $message->setBadge( $payload->badge );
    }
    if (isset($payload->sound)) {
      $message->setSound( $payload->sound );
    }
    if (isset($payload->expiry)) {
      $message->setExpiry( $payload->expiry );
    }
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
   * @param $tokens
   * @param Payload $payload
   * @return \PHP_GCM\MulticastResult|\Psr\Http\Message\ResponseInterface
   */
  public function sendAndroid ( $tokens, Payload $payload ) {
    if ( $this->options->oldGcmMode ) {
      return $this->sendAndroidGcm( $tokens, $payload );
    }
    else {
      return $this->sendAndroidFcm( $tokens, $payload );
    }
  }

  /**
   * @param $tokens string[]
   * @param Payload $payload
   * @return \PHP_GCM\MulticastResult
   */
  public function sendAndroidFcm ( $tokens, Payload $payload ) {

    $connection = $this->getAndroidConnection();
    $message = new Message();
    foreach ( $tokens as $token ) {
      $message->addRecipient( new Device( $token ) );
    }
    $message->setData( [
      "title" => $payload->title,
      "body" => $payload->body,
      "expiry" => $payload->expiry,
      "type" => $payload->type,
      "data" => $payload->data,
      "badge" => $payload->badge,
    ] );

    return $connection->send( $message );
  }

  /**
   * @param $tokens
   * @param Payload $payload
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function sendAndroidGcm ( $tokens, Payload $payload ) {

    $connection = $this->getAndroidConnection();

    $message = new \PHP_GCM\Message( null, [
      "title" => $payload->title,
      "message" => $payload->body,
      "type" => $payload->type,
      "data" => $payload->data,
      "badge" => $payload->badge,
    ] );

    return $connection->send( $message, $tokens[ 0 ], 10 );
  }


  private function getAndroidConnection () {
    if ( !$this->gcmConnection ) {
      if ( $this->options->oldGcmMode ) {
        $this->gcmConnection = new Sender( $this->options->gcmKey );
      }
      else {
        $this->gcmConnection = new Client();
        $this->gcmConnection->setApiKey( $this->options->gcmKey );
        $this->gcmConnection->injectHttpClient( new \GuzzleHttp\Client() );
      }
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
    return $device->platform === $platform;
  } ) );

  return array_map( function ( $device ) {
    return $device->token;
  }, $platformDevices );
}