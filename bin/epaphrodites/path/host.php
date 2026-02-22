<?php

namespace Epaphrodites\epaphrodites\path;

class host
{

  protected string $host;

  /**
   * Get domaine of website
   * @return string
   */
  private function domain()
  {

    return _DOMAINE_;
  }

  /**
   * Host link path
   * @return string
   */
  private function getHost():string
  {
      $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
  
      $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
  
      $this->host = $protocol . $httpHost . '/' . $this->domain();
  
      return $this->host;
  }

  /**
   * @return string
   */
  public function host():string
  {
    
    return $this->getHost();
  }
  
}