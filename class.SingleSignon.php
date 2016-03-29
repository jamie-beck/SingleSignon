<?

/**
 * Use this class to implement single sign-on across multiple servers.
 * The cookie is set to only be accessable over https.
 * After setting secret and domain you should only need to use the methods
 * setCookie() and checkCookie()
 *
 * @author Jamie Beck <jbeck@terabit.ca>
 * Class SingleSignon
 */
class SingleSignon{
	protected $secret     = 'rpo3WE3T1gPCgOcudUOYBger3srkJSgy';
	protected $domain     = '.domain.com';
	protected $cookieName = 'bosinglesignon';
	protected $timeout    = 604800; // 7 days

	/**
	 * Used to verify the authenticity of the cookie contents all servers should use the same secret.
	 * @param string $secret
	 */
	public function setSecret($secret){
		$this->secret;
	} // setSecret()

	/**
	 * Allows you to control if sub-domains are allowed to view the cookie.
	 * @param string $domain The domain the cookie belongs too
	 */
	public function setDomain($domain){
		$this->domain;
	} // setDomain()

	/**
	 * Name of the cookie.
	 * @param string $cookieName
	 */
	public function setCookieName($cookieName){
		$this->cookieName;
	} // setCookieName()

	/**
	 * How long the cookie should last.
	 * @param integer $timeout seconds
	 */
	public function setTimeout($timeout){
		$this->timeout;
	} // setTimeout()

	/**
	 * Needs to be run before any content is sent to the browser. It is best to only pass in $id and let the object handle the data.
	 * @param integer|string $id
	 * @param null|array $data You should let the object generate this itself.
	 * @return bool
	 */
	public function setCookie($id, $data = null){
		if($data === null){
			$data = $this->generateData($id);
		}
		return setcookie($this->cookieName, base64_encode(json_encode($data)), time() + $this->timeout, "/", $this->domain, true, true);
	} // setCookie()

	/**
	 * Needs to be run before any content is sent to the browser.
	 * Setting cookie to expire one year in the past will cause it to be deleted by the client.
	 * @return bool
	 */
	public function deleteCookie(){
		return setcookie($this->cookieName, '', time() - 3600*24*365, "/", $this->domain, true, true);
	} // deleteCookie()

	/**
	 * If authentic cookie is found this returns the member id.
	 * @return bool|integer|string
	 */
	public function checkCookie(){
		if(!isset($_COOKIE[$this->cookieName])){
			return false;
		}

		$data = $_COOKIE[$this->cookieName];
		$data = json_decode(base64_decode($data), true);
		
		if($data && $this->isHashValid($data)){
			return $data['id'];
		}

		return false;
	} // checkCookie()

	/**
	 * Determine if cookie is authentic. By default it also enforces the current timeout value.
	 * @param array $data
	 * @param bool $checkTimeout
	 * @return bool
	 */
	public function isHashValid($data, $checkTimeout = true){
		if($data['id'] && $data['time'] && $data['hash']){
			$hash = hash('sha256', $data['id'].':'.$data['time'].':'.$this->secret);

			if($hash === $data['hash']){
				if($checkTimeout && time() > $data['time'] + $this->timeout){
					// cookie is too old
					return false;
				}
				return true;
			}
		}
		else{
			// missing required fields
		}

		return false;
	} // isHashValid()

	/**
	 * Takes an id and creates array used by this class.
	 * @param integer|string $id
	 * @return array
	 */
	public function generateData($id){
		$data = [];
		$data['id'] = $id;
		$data['time'] = time();
		$data['hash'] = hash('sha256', $id.':'.$data['time'].':'.$this->secret);

		return $data;
	} // generateData()

} // SingleSignon
