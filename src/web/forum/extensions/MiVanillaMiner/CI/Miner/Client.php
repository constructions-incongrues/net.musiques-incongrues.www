<?php
class CI_Miner_Client
{
	/**
	 * URL to Miner instance.
	 * @var string
	 */
	private $urlMiner;

	/**
	 * Cache backend instance
	 * @var Zend_Cache_Backend
	 */
	private $cacheBackend;

	/**
	 * Holds self instance. Used for singleton.
	 * @var CI_Miner_Client
	 */
	private static $instance;

	public static function getInstance($urlMiner = null, Zend_Cache_Backend $cacheBackend = null)
	{
		if (is_null(self::$instance)) {
			self::$instance = new CI_Miner_Client($urlMiner, $cacheBackend);
		}

		return self::$instance;
	}

	public function __construct($urlMiner, Zend_Cache_Backend $cacheBackend)
	{
		// Store URL to service
		$this->urlMiner = $urlMiner;

		// Store cache backend instance
		$this->cacheBackend = $cacheBackend;
	}

	public function postResources(array $resources)
	{
		foreach ($resources as $resource) {
			$this->postResource($resource);
		}
	}

	public function postResource(array $resource)
	{

	}

	/**
	 * Queries a collection in Miner instance and caches response.
	 *
	 * @param string $collection Name of collection
	 * @param string $segment    Name of collection's segment
	 * @param array  $parameters Query parameters
	 *
	 * @return array Query results
	 */
	public function query($collection, $segment = 'all', array $parameters = array())
	{
		// Create cache tags array
		// TODO : externalize cache tags creation as strategy ?
		$cacheTags = array();
		if (isset($parameters['discussion_id'])) {
			$cacheTags[] = 'discussions';
			$cacheTags[] = sprintf('discussion_%d', $parameters['discussion_id']);
		}

		// Instanciate cache handler.
		// NOTE : Instanciation is done every time becauses we need to redefine tags array
		$cacheFrontend = new Zend_Cache_Frontend_Class(array('cached_entity' => $this));
		$cacheFrontend->setTagsArray($cacheTags);
		$cache = Zend_Cache::factory($cacheFrontend, $this->cacheBackend);

		// Query service and cache response
		$response = $cache->doQuery($collection, $segment, $parameters);

		// Return response
		return $response;
	}

	/**
	 * Queries a collection in Miner instance.
	 * Does not handle cache. Usage of CI_Miner_Client::query() is prefered.
	 *
	 * @param string $collection Name of collection
	 * @param string $segment    Name of collection's segment
	 * @param array  $parameters Query parameters
	 *
	 * @return array Query results
	 */
	public function doQuery($collection, $segment = 'all', array $parameters = array())
	{
		// Build URL to segment query endpoint
		$urlRoot = sprintf('%s/collections/%s/segments/%s/get', $this->urlMiner, $collection, $segment);

		// Force response format to JSON
		$parameters['format'] = 'json';

		// Build query URL
		$urlQuery = sprintf('%s?%s', $urlRoot, http_build_query($parameters));

		// Instanciate and configure cURL
		$curl = curl_init($urlQuery);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// Make call and handle response
		// TODO : Handle error responses
		$responseJson = curl_exec($curl);
		$responseArray = json_decode($responseJson, true);

		return $responseArray;
	}
}
