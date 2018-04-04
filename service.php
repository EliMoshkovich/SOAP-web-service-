<?php
	
		// This function creates the tables and inserts data only if the table is not exists already.
	function readJsonAndCreateDB(){
		$sqlite = new SQLiteHandler((new SQLiteConnection())->connect());
		// create new tables
		if(!$sqlite->tableExist()){
			print "inserting data!";
			$sqlite->createTables();	
			
			$str = file_get_contents('http://localhost:6543/coupons_data.json');
			$json = json_decode($str, true);
			$coupons_array = $json['response']['offerCouponsList'];
			
			foreach ($coupons_array as $index => $jsons) { 
				 foreach($jsons as $key => $value) {
					 if($value === false){
						$coupons_array[$index][$key] = 0;
					 }
					 else if($value === true){
						$coupons_array[$index][$key] = 1;
					 }
				}
			}
			
			foreach ($coupons_array as &$coupon) {
				$sqlite->insertRow($coupon);
			}
		}
	}
	readJsonAndCreateDB();	// Creating DB and data.
	
	// This function handles the GET request.
	function getCoupons(){
		// Getting all data from DB and returning an array.
		$sqlite = new SQLiteHandler((new SQLiteConnection())->connect());
		if(!$sqlite->tableExist()){
			return "Server is up and running, Please refresh";
			/*readJsonAndCreateDB();
			$RESPONSE_FROM_DB = $sqlite->getAllCoupos();
			return json_encode($RESPONSE_FROM_DB);*/
		}
		$RESPONSE_FROM_DB = $sqlite->getAllCoupos();
		return json_encode($RESPONSE_FROM_DB);
		//return "test";
	}
	
	// This function handles the POST request.
	function setClippedCoupon($couponId){
		$sqlite = new SQLiteHandler((new SQLiteConnection())->connect());
		return $sqlite->checkAndClipCoupon($couponId);
	}
	
	
	// Setting the soap server
	ini_set('soap.wsdl_cache_enabled',0);
	ini_set('soap.wsdl_cache_ttl',0);
	$server = new SoapServer('service.wsdl',array('encoding'=>'UTF-8'));
	$server->addFunction("setClippedCoupon");
	$server->addFunction("getCoupons");
	$server->handle();
	echo getCoupons();
	
	// SQL Handler class
	class SQLiteHandler {
 
		private $pdo;
	 
		/**
		 * connect to the SQLite database
		 */
		public function __construct($pdo) {
			$this->pdo = $pdo;
		}
	 
		public function createTables() {
			
			$command = 
				'CREATE TABLE IF NOT EXISTS coupons (
						inmarID INTEGER PRIMARY KEY,
						issuer  VARCHAR (255),
						issuerCode VARCHAR (255),
						offerCode VARCHAR (255),
						activeDate DATETIME ,
						expirationDate INTEGER,
						dateSelected TIMESTAMP,
						dateRedeemed TIMESTAMP,
						clipStartDate TIMESTAMP,
						clipEndDate TIMESTAMP,
						fundingType VARCHAR (255),
						clipType VARCHAR (255),
						program VARCHAR (255),
						brand VARCHAR (255),
						offerType VARCHAR (255),
						offerValue INTEGER,
						offerValueType INTEGER,
						upcaCode INTEGER,
						shortDescription VARCHAR (255),
						offerDescription VARCHAR (255),
						detailLink VARCHAR(2083),
						offerStatus VARCHAR (255),
						imageURL VARCHAR(255),
						displayDateName DATETIME,
						displayDateValue DATETIME,
						newCoupon BOOLEAN,
						offerDisplayValue VARCHAR(255),
						rewardId INTEGER,
						sortOfferValue INTEGER,
						rewardIsGiftcard BOOLEAN,
						category VARCHAR(255),
						validatingUPCs VARCHAR(255),
						rank VARCHAR(255),
						keywords VARCHAR(255),
						displayExpirationDate DATETIME,
						displayActiveDate DATETIME,
						displayDateRedeemed DATETIME,
						buyQuantity INTEGER,
						clipped BOOLEAN)';
						
			$this->pdo->exec('DROP TABLE coupons;');		
			return $this->pdo->exec($command);
			
		}
		
		public function insertRow($obj){
			$command = 'INSERT INTO coupons (inmarID,issuer,issuerCode,offerCode,activeDate,expirationDate,dateSelected,dateRedeemed,clipStartDate,clipEndDate
			,fundingType,clipType,program,brand,offerType,offerValue,offerValueType,upcaCode,shortDescription,
			offerDescription,detailLink,offerStatus,imageURL,displayDateName,displayDateValue,newCoupon,offerDisplayValue,rewardId,sortOfferValue,rewardIsGiftcard,
			category,validatingUPCs,rank,keywords,displayExpirationDate,displayActiveDate,displayDateRedeemed,buyQuantity) VALUES (:inmarID,:issuer,
			:issuerCode,:offerCode,:activeDate,:expirationDate,:dateSelected,:dateRedeemed,:clipStartDate,:clipEndDate,:fundingType,:clipType,:program,
			:brand,:offerType,:offerValue,:offerValueType,:upcaCode,:shortDescription,:offerDescription,:detailLink,:offerStatus,:imageURL,:displayDateName,
			:displayDateValue,:newCoupon,:offerDisplayValue,:rewardId,:sortOfferValue,:rewardIsGiftcard,:category,:validatingUPCs,:rank,:keywords,:displayExpirationDate,
			:displayActiveDate,:displayDateRedeemed,:buyQuantity)';
			
			$stmt = $this->pdo->prepare($command);
			$stmt->execute([
				':inmarID' => $obj['inmarID'],
				':issuer' => $obj['issuer'],
				':issuerCode' => $obj['issuerCode'],
				':offerCode' => $obj['offerCode'],
				':activeDate' => $obj['activeDate'],
				':expirationDate' => $obj['expirationDate'],
				':dateSelected' => $obj['dateSelected'],
				':dateRedeemed' => $obj['dateRedeemed'],
				':clipStartDate' => $obj['clipStartDate'],
				':clipEndDate' => $obj['clipEndDate'],
				':fundingType' => $obj['fundingType'],
				':clipType' => $obj['clipType'],
				':program' => $obj['program'],
				':brand' => $obj['brand'],
				':offerType' => $obj['offerType'],
				':offerValue' => $obj['offerValue'],
				':offerValueType' => $obj['offerValueType'],
				':upcaCode' => $obj['upcaCode'],
				':shortDescription' => $obj['shortDescription'],
				':offerDescription' => $obj['offerDescription'],
				':detailLink' => $obj['detailLink'],
				':offerStatus' => $obj['offerStatus'],
				':imageURL' => $obj['imageURL'],
				':displayDateName' => $obj['displayDateName'],
				':displayDateValue' => $obj['displayDateValue'],
				':newCoupon' => $obj['newCoupon'],
				':offerDisplayValue' => $obj['offerDisplayValue'],
				':rewardId' => $obj['rewardId'],
				':sortOfferValue' => $obj['sortOfferValue'],
				':rewardIsGiftcard' => $obj['rewardIsGiftcard'],
				':category' => $obj['category'],
				':validatingUPCs' => $obj['validatingUPCs'],
				':rank' => $obj['rank'],
				':keywords' => $obj['keywords'],
				':displayExpirationDate' => $obj['displayExpirationDate'],
				':displayActiveDate' => $obj['displayActiveDate'],
				':displayDateRedeemed' => $obj['displayDateRedeemed'],
				':buyQuantity' => $obj['buyQuantity']
			]);
			
			$this->pdo->exec($command);
		}
		
		public function getAllCoupos(){
			$query = 'SELECT * FROM coupons';
			$result = $this->pdo->query($query);
			$coupons = [];
			foreach($result as $row){
				// Converting boolean fields from 0,1 back to true,false.
				if($row['clipped'] === '1'){
					$row['clipped'] = true;
				}
				else if($row['clipped'] === null || $row['clipped'] === '0'){
					$row['clipped'] = false;
				}
				if($row['newCoupon'] === '1'){
					$row['newCoupon'] = true;
				}
				else if($row['newCoupon'] === null || $row['newCoupon'] === '0'){
					$row['newCoupon'] = false;
				}
				if($row['rewardIsGiftcard'] === '1'){
					$row['rewardIsGiftcard'] = true;
				}
				else if($row['rewardIsGiftcard'] === null || $row['rewardIsGiftcard'] === '0'){
					$row['rewardIsGiftcard'] = false;
				}
				
				$coupons[] = [
					'inmarID' => $row['inmarID'],
					'issuer' => $row['issuer'],
					'issuerCode' => $row['issuerCode'],
					'offerCode' => $row['offerCode'],
					'activeDate' => $row['activeDate'],
					'expirationDate' => $row['expirationDate'],
					'dateSelected'=> $row['dateSelected'],
					'dateRedeemed'=> $row['dateRedeemed'],
					'clipStartDate'=> $row['clipStartDate'],
					'clipEndDate'=> $row['clipEndDate'],
					'fundingType'=> $row['fundingType'],
					'clipType'=> $row['clipType'],
					'program'=> $row['program'],
					'brand'=> $row['brand'],
					'offerType'=> $row['offerType'],
					'offerValue'=> $row['offerValue'],
					'offerValueType'=> $row['offerValueType'],
					'upcaCode'=> $row['upcaCode'],
					'shortDescription'=> $row['shortDescription'],
					'offerDescription'=> $row['offerDescription'],
					'detailLink'=> $row['detailLink'],
					'offerStatus'=> $row['offerStatus'],
					'imageURL'=> $row['imageURL'],
					'displayDateName'=> $row['displayDateName'],
					'displayDateValue'=> $row['displayDateValue'],
					'newCoupon'=> $row['newCoupon'],
					'offerDisplayValue'=> $row['offerDisplayValue'],
					'rewardId'=> $row['rewardId'],
					'sortOfferValue'=> $row['sortOfferValue'],
					'rewardIsGiftcard'=> $row['rewardIsGiftcard'],
					'category'=> $row['category'],
					'validatingUPCs'=> $row['validatingUPCs'],
					'rank'=> $row['rank'],
					'keywords'=> $row['keywords'],
					'displayExpirationDate'=> $row['displayExpirationDate'],
					'displayActiveDate'=> $row['displayActiveDate'],
					'displayDateRedeemed'=> $row['displayDateRedeemed'],
					'buyQuantity'=> $row['buyQuantity'],
					'clipped'=> $row['clipped']
				];
			}
			 
			return $coupons;

		}
		
		public function updateClippedCoupon($coupon_id){
			$query = "UPDATE coupons SET clipped = 1 WHERE inmarID = :coupon_id";
				
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':coupon_id', $coupon_id);
	 
			// execute the update statement
			$stmt->execute();
		}
		
		public function checkAndClipCoupon($coupon_id){
			$query  = "SELECT * FROM coupons WHERE inmarID =" . $coupon_id . ";";
			$result = $this->pdo->query($query);
			$coupons = [];
			foreach($result as $row){
				$coupons[] = [
					'inmarID' => $row['inmarID'],
					'clipped' => $row['clipped']
				];
			}
			if(count($coupons) > 0){
				if($coupons[0]['clipped'] === '1'){
					return 'ERROR: coupon already clipped';
				}
				else{
					$this->updateClippedCoupon($coupon_id);
					return "UPDATED";
				}
			}
			else{
				return "ERROR: unexisting ID";
			}
			
		}
	
		public function tableExist() {
			$stmt = $this->pdo->query("SELECT name
									   FROM sqlite_master
									   WHERE type = 'table'
									   ORDER BY name");
			$tables = [];
			while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
				$tables[] = $row['name'];
			}
			if(count($tables) > 0) return true; else return false;
		}
	}
		
	class Config {
	   /**
		* path to the sqlite file
		*/
		const PATH_TO_SQLITE_FILE = './phpsqlite.db';
	 
	}
	
	class SQLiteConnection {
		/**
		 * PDO instance
		 * @var type 
		 */
		private $pdo;
	 
		/**
		 * return in instance of the PDO object that connects to the SQLite database
		 * @return \PDO
		 */
		public function connect() {
			if ($this->pdo == null) {
				$this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
			}
			return $this->pdo;
		}
	}
	
?>

