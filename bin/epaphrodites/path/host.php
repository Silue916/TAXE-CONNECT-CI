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
      $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
      $isHttps = (
          (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
          || strtolower((string) $forwardedProto) === 'https'
      );

      $protocol = $isHttps ? 'https://' : 'http://';

      $httpHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');

      $domain = trim((string) $this->domain(), '/');
      $suffix = $domain !== '' ? '/' . $domain : '';

      $this->host = $protocol . $httpHost . $suffix . '/';

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
