<?php
/**
 * V3Application
 *
 * V3ctor WareHouse Web Application 
 *
 * Copyright 2016 Jorge Alberto Ponce Turrubiates
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   V3Application
 * @package    V3Application
 * @copyright  Copyright 2016 Jorge Alberto Ponce Turrubiates
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    1.0.0, 2016-01-12
 * @author     Jorge Alberto Ponce Turrubiates (the.yorch@gmail.com)
 */
class V3Application
{
	/**
	 * V3ctorWH Key
	 *
	 * @var string $_key V3ctorWH Key
	 * @access private
	 */
	private $_key = null;

	/**
	 * V3ctorWH Application
	 *
	 * @var string $_app V3ctorWH Application
	 * @access private
	 */
	private $_app = null;

	/**
	 * Supported Databases
	 */
	const MONGODB = 'v3Mongo';
	const MSSQLSERVER = 'v3MySQL';
	const MYSQL = 'v3SQLSrv';

	/**
	 * Create a Slim WebServices Application
	 *
	 * @param string $dbtype   DataBase Type
	 * @param string $hostname Hostname
	 * @param string $username User Name
	 * @param string $password Password
	 * @param string $dbname   DataBase Name
	 * @param string $port     DataBase Name
	 * @param string $key      V3ctor WareHouse Key
	 */
	public function __construct($dbtype = self::MONGODB, $hostname, $username, $password, $dbname, $port, $key) {
		$this->_key = $key;
		$this->_app = $dbname;

		// Load Slim Application
		\Slim\Slim::registerAutoloader();

		$app = new \Slim\Slim();

		// Run WebServices Application
		$v3ctor = V3WareHouse::getInstance($dbtype, $hostname, $username, $password, $dbname, $port);

		if (! $v3ctor->isConnected()){
			$vector->setKey($key);

		    $msg = array("error" => 'Unable load V3ctor WareHouse');

		    die(json_encode($msg));
		}

		// Welcome
		$app->get(
		    '/',
		    function () use ($app, $v3ctor) {
		        $app->response()->header('Content-Type', 'application/json');
		        
		        $app->response()->status(200);

		        $msg = array("msg" => 'Welcome to V3ctor WareHouse Application ' . $this->_app);

		        echo json_encode($msg);
		    }
		);

		// Gets Object by _id
		$app->get(
		    '/(:entity)/(:id)',
		    function ($entity, $id) use ($app, $v3ctor) {
		    	$this->checkKey($app);

		        $app->response()->header('Content-Type', 'application/json');
		        $app->response()->status(200);  
		        
		        echo json_encode($v3ctor->findObject($entity, $id));
		    }
		);

		// Sets a New Object
		$app->post(
		    '/(:entity)',
		    function ($entity) use ($app, $v3ctor) {
		    	$this->checkKey($app);

		        try{
		            $body = $app->request->getBody();
		            $jsonData = json_decode($body);

		            $app->response()->header('Content-Type', 'application/json');
		            $app->response()->status(200);  
		            
		            echo json_encode($v3ctor->newObject($entity, $jsonData));
		        }
		        catch (ResourceNotFoundException $e) {
		            $app->response()->status(404);
		        } 
		        catch (Exception $e) {
		            $app->response()->status(400);
		            $app->response()->header('X-Status-Reason', $e->getMessage());
		        }
		    }
		);

		// Update a Object
		$app->put(
		    '/(:entity)/(:id)',
		    function ($entity, $id) use ($app, $v3ctor) {
		    	$this->checkKey($app);

		        try{
		            $body = $app->request->getBody();
		            $jsonData = json_decode($body);

		            $app->response()->header('Content-Type', 'application/json');
		            $app->response()->status(200);  
		            
		            $result = $v3ctor->updateObject($entity, $id, $jsonData);

		            $msgOk = array('msg' => 'OK');
		            $msgBad = array('msg' => 'ERROR');

		            if ($result)
		                echo json_encode($msgOk);
		            else
		                echo json_encode($msgBad);
		        }
		        catch (ResourceNotFoundException $e) {
		            $app->response()->status(404);
		        } 
		        catch (Exception $e) {
		            $app->response()->status(400);
		            $app->response()->header('X-Status-Reason', $e->getMessage());
		        }
		    }
		);

		// Delete a Object
		$app->delete(
		    '/(:entity)/(:id)',
		    function ($entity, $id) use ($app, $v3ctor) {   
		    	$this->checkKey($app);

		        $app->response()->header('Content-Type', 'application/json');
		        $app->response()->status(200);  
		        
		        $result = $v3ctor->deleteObject($entity, $id);

		        $msgOk = array('msg' => 'OK');
		        $msgBad = array('msg' => 'ERROR');

		        if ($result)
		            echo json_encode($msgOk);
		        else
		            echo json_encode($msgBad);
		    }
		);

		// Find Objects by Query
		$app->post(
		    '/query/(:entity)',
		    function ($entity) use ($app, $v3ctor) {
		    	$this->checkKey($app);

		        try{
		            $body = $app->request->getBody();
		            
		            $jsonQuery = json_decode($body);

		            $app->response()->header('Content-Type', 'application/json');
		            $app->response()->status(200);

		            $jsonQuery = (array) $jsonQuery;

		            echo json_encode($v3ctor->query($entity, $jsonQuery));
		        }
		        catch (ResourceNotFoundException $e) {
		            $app->response()->status(404);
		        } 
		        catch (Exception $e) {
		            $app->response()->status(400);
		            $app->response()->header('X-Status-Reason', $e->getMessage());
		        }
		    }
		);

		// Not Sent Key
		$app->get(
		    '/notkey',
		    function () use ($app) {
		        $app->response()->header('Content-Type', 'application/json');
		        $app->response()->status(404);

		        $msg = array("error" => "Not Sent Key");

		        echo json_encode($msg);
		    }
		);

		// Not Valid Key
		$app->get(
		    '/invalidkey',
		    function () use ($app) {
		        $app->response()->header('Content-Type', 'application/json');
		        $app->response()->status(404);

		        $msg = array("error" => "Permission denied");
		        
		        echo json_encode($msg);
		    }
		);

		// Run Slim Application
		$app->run();
	}

	/**
	 * Validate Sent Key
	 */
	private function checkKey(){
		$app = \Slim\Slim::getInstance();
		$key = $app->request->params('auth');

	    if (!$key){
	        $app->redirect('/notkey');
	    }
	    else{
	        if ($key != $this->_key){
	            $app->redirect('/invalidkey');
	        }
	    }
	}
}

?>