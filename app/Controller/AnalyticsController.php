<?php
class AnalyticsController extends AppController {
 
    public $components = array('Session');
	public function beforeFilter(){
	parent::beforeFilter();
		$this->Auth->allow('index');
	}
 
    public function index() {
	    $path = '/path/to/cakephp/app/Vendor/google-api-php-client/src';
	    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
 
        App::import('Vendor', 'Google_Client', array('file' => 'google-api-php-client/src/Google/autoload.php'));
        App::import('Vendor', 'Google_Client', array('file' => 'google-api-php-client/src/Google/Client.php'));
        App::import('Vendor', 'Google_Service_Analytics', array('file' => 'google-api-php-client/src/Google/Service/Analytics.php'));
 
        define('CLIENT_ID', '260525851176-lhir1mpsc9c37b940rrs4s9j3q75kebd.apps.googleusercontent.com');
        define('CLIENT_SECRET', 'BefdR4Ahb2KL2ng6cPcbUJXA');
        define('REDIRECT_URI', 'http://eroparts.com/analytics');
 
        $client = new Google_Client();
        $client->setClientId(CLIENT_ID);
        $client->setClientSecret(CLIENT_SECRET);
        $client->setRedirectUri(REDIRECT_URI);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');
        $analytics = new Google_Service_Analytics($client);
 
        if (isset($this->request->query['code'])) {
            $client->authenticate($this->request->query['code']);
            $this->Session->write('token', $client->getAccessToken());
            $this->redirect('http://' . $_SERVER['HTTP_HOST'] . '/analytics');
        }
 
        if ($this->Session->check('token')) {
            $client->setAccessToken($this->Session->read('token'));
        }
         
        if ($client->getAccessToken()) {
            $start_date = date('Y-m-d', strtotime('- 10 day'));
            $end_date = date('Y-m-d');
            $view = '106921167';
 
            $data = array();
            $dimensions = 'ga:date';
            $metrics = 'ga:visits';
            $sort = 'ga:date';
            $optParams = array('dimensions' => $dimensions, 'sort' => $sort);
            $results = $analytics->data_ga->get('ga:' . $view, $start_date, $end_date, $metrics, $optParams);
            if (isset($results['rows']) && !empty($results['rows'])) {
                $data['Sample']['date'] = $results['rows'][0][0];
                $data['Sample']['visits'] = $results['rows'][0][1];
            }
pr($data); 
        } else {
            $auth_url = $client->createAuthUrl();
            echo '<a href="'.$auth_url.'">click</a>';
        }
        exit;
    }
}
