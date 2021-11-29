<?php namespace Placewise;

  /**
   *  Placewise REST API Client
   */
  class Client
  {
    private $email;
    private $password;
    private $auth_id;
    private $authentication_token;

    function __construct($email, $password) {
      $this->email    = $email;
      $this->password = $password;
      $this->base_url = 'https://api.placewise.com';
    }

    /**
    *
    *  Get a resource from the Placewise REST API
    *
    *  @param string $path The URL path for the resource to access
    *
    *  @api
    *  @return array
    */
    function get($path, $params = []) {
      if (!$this->auth_id) $this->login();
      $response = $this->get_from_api("$this->base_url/$path", $params);

      return $response;
    }

    function login() {
      $credentials = [
        'data' => [
          'attributes' => [
            'email'    => $this->email,
            'password' => $this->password
          ]
        ],
        'type' => 'accounts'
      ];
      $session = $this->post_to_api("$this->base_url/accounts/login", $credentials);

      $this->auth_id    	 				= $session->data->id;
    	$this->authentication_token = $session->data->attributes->authentication_token;
    }

    private function get_from_api($url, $params = []) {
      $timestamp  = time();
      $auth_token = md5($timestamp . ':' . $this->authentication_token);

      $url = "$url?auth_id=$this->auth_id&auth_token=$auth_token&auth_timestamp=$timestamp";

      if ($params['page']) {
        $params['page']['number'] = $params['page']['number'] ?: 1;
        $params['page']['size']   = $params['page']['size'] ?: 25;
        $url = $url . '&page[number]=' . $params['page']['number'] . '&page[size]=' . $params['page']['size'];
      }

      if ($params['include']) {
        $url = $url . '&include=' . $params['include'];
      }

      if ($params['fields']) {
        foreach ($params['fields'] as $key => $value) {
          $url = "$url&fields[$key]=$value";
        }
      }

    	$ch = curl_init();

    	$headers = ['Accept: application/vnd.api+json', 'Content-Type: application/vnd.api+json'];

    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    	$response = json_decode(curl_exec ($ch));
    	curl_close ($ch);

    	return $response;
    }

    private function post_to_api($url, $body, $params = []) {
      $headers = ['Accept: application/vnd.api+json', 'Content-Type: application/vnd.api+json'];

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    	$result = json_decode(curl_exec ($ch));
    	curl_close ($ch);

    	return $result;
    }
  }

?>
