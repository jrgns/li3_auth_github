<?php
namespace li3_auth_github\controllers;
use \lithium\core\Libraries;
use \lithium\net\http\Service;
use \lithium\storage\Session;
use \lithium\security\Auth;
class AuthController extends \lithium\action\Controller {
    protected $service = null;

    public function check()
    {
        $token = Session::read('auth_github.token');
        if (empty($token)) {
            $client_id    = Libraries::get('li3_auth_github', 'client_id');
            $redirect_url = Libraries::get('li3_auth_github', 'redirect');
            if (empty($redirect_url)) {
                $redirect_url = 'http://jrgns/lithium/login/github/requested';
            }
            $params = http_build_query(compact('client_id', 'redirect_url'));
            return $this->redirect('https://github.com/login/oauth/authorize?' . $params);
        }
        $response = $this->getService(array('host' => 'api.github.com'))->get('/user', array('access_token' => $token));
        if (empty($response)) {
            throw new \Exception('Failed Github Request: ' . $response);
        }
        $response = @json_decode($response, $response);
        if (!empty($response['error'])) {
            throw new \Exception('Github Error: ' . $response['error']);
        }
        $model = Libraries::locate('models', 'Users');
        $user  = $model::findByEmail($response['email']);
        if (!$user) {
            $user = $model::create();
            $user->username = $response['login'];
            $user->email    = $response['email'];
            $user->active   = 1;
            $user->save();
        }
        Auth::set('default', $user);
        return $this->redirect('Stories::index');
    }

    public function requested()
    {
        if (empty($this->request->query['code'])) {
            return $this->redirect('Auth::check');
        }
        $data = array(
            'client_id'     => Libraries::get('li3_auth_github', 'client_id'),
            'client_secret' => Libraries::get('li3_auth_github', 'client_secret'),
            'code'          => $this->request->query['code'],
        );
        $response = $this->getService()->post('/login/oauth/access_token', $data);
        if (empty($response)) {
            throw new \Exception('Failed Github Request: ' . $response);
        }
        parse_str($response, $response);
        if (!empty($response['error'])) {
            throw new \Exception('Github Error: ' . $response['error']);
        }
        if (!Session::write('auth_github.token', $response['access_token'])) {
            throw new \Exception('Could not store OAuth token');
        }
        return $this->redirect('Auth::check');
    }

    protected function getService(array $options = array())
    {
        $default = array(
            'scheme' => 'https',
            'host'   => 'github.com',
        );
        $options = array_merge($default, $options);
        if (empty($this->service)) {
            $this->service = new Service($options);
        }
        return $this->service;
    }
}
