<?php
/**
 * Telegram Bot example.
 * @author Gabriele Grillo <gabry.grillo@alice.it>
  * designed starting from https://github.com/Eleirbag89/TelegramBotPHP

 */
include(dirname(__FILE__).'/../settings.php');
include('settings_t.php');
include(dirname(dirname(__FILE__)).'/getting.php');
include("Telegram.php");
include("broadcast.php");
include("QueryLocation.php");

class main{

 function start($telegram,$update)
	{

		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");

		// Instances the class
		$data=new getdata();
		$db = new PDO(DB_NAME);

		/* If you need to manually take some parameters
		*  $result = $telegram->getData();
		*  $text = $result["message"] ["text"];
		*  $chat_id = $result["message"] ["chat"]["id"];
		*/

		$text = $update["message"] ["text"];
		$chat_id = $update["message"] ["chat"]["id"];
		$user_id=$update["message"]["from"]["id"];
		$location=$update["message"]["location"];
		$reply_to_msg=$update["message"]["reply_to_message"];

		$this->shell($telegram, $db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg);
$db = NULL;
	}

	//gestisce l'interfaccia utente
	 function shell($telegram,$db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg)
	{
		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");

		if ($text == "/start") {
				$log=$today. ";new chat started;" .$chat_id. "\n";
			}
			//richiedi previsioni meteo di oggi
			elseif ($text == "/meteo oggi" || $text == "meteo oggi") {
        $reply = "Previsioni Meteo per oggi:\n" .$data->get_forecast("Lecceoggi");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        $log=$today. ";previsioni Lecce sent;" .$chat_id. "\n";
				}
			//richiede previsioni meteo di domani
			elseif ($text == "/previsioni" || $text == "previsioni") {

        $reply = "Previsioni Meteo :\n" .$data->get_forecast("Lecce");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        $log=$today. ";previsioni Lecce sent;" .$chat_id. "\n";
			}	//richiede rischi di oggi a Lecce
  			elseif ($text == "/bollettini rischi" || $text == "bollettini rischi") {
          $reply = "Allerta Meteo Protezione Civile Lecce:\n" .$data->get_allertameteo("Lecceoggi");
          $content = array('chat_id' => $chat_id, 'text' => $reply);
          $telegram->sendMessage($content);

  				$log=$today. ";rischi sent;" .$chat_id. "\n";

  			}
			//richiede rischi di oggi a Lecce
			elseif ($text == "/aria" || $text == "qualità aria") {
      $reply = $data->get_aria("lecce");
      $reply .="\nTabella valori di riferimento e info: http://goo.gl/H1nPxO";

      $content = array('chat_id' => $chat_id, 'text' => $reply);
      $telegram->sendMessage($content);

				$log=$today. ";aria sent;" .$chat_id. "\n";

			}elseif ($text == "/traffico" || $text == "traffico") {
      $reply = "Segnalazione Demo/Test non reale".$data->get_traffico("lecce");
      $content = array('chat_id' => $chat_id, 'text' => $reply);
      $telegram->sendMessage($content);
				$log=$today. ";traffico sent;" .$chat_id. "\n";

			}elseif ($text == "/Lecce Events" || $text == "Lecce Events") {
        $reply = "Eventi culturali in programmazione:\n" .$data->get_events("eventioggi");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);

				$log=$today. ";eventi sent;" .$chat_id. "\n";
			}
			//crediti
			elseif ($text == "/informazioni" || $text == "informazioni") {
				 $reply = ("openDataLecceBot e' un servizio sperimentale e dimostrativo per segnalazioni meteo e rischio a Lecce.
				 Puoi:
				 - selezionare un'etichetta in basso,
				 - digitare /on o /off nella chat per abilitare o disabili  tare le notifiche automatiche
				 - mappare una segnalazione inviando la posizione tramite la molletta in basso a sinistra.
				 Applicazione sviluppata da Piero Paolicelli @piersoft (agosto 2015). Licenza MIT codice in riuso da : http://iltempe.github.io/Emergenzeprato/
          \nFonti:
          Bollettini rischi   -> Protezione Civile di Lecce su dati.comune.lecce.it tramite il programma InfoAlert365
          Eventi culturali    -> piattaforma dati.comune.lecce.it fonte Lecce Events
          Qualtà dell'Aria    -> piattaforma dati.comune.lecce.it
          Farmacie            -> piattaforma dati.comune.lecce.it
          Benzinai            -> piattaforma openstreemap Lic. odBL
          Musei               -> piattaforma openstreemap Lic. odBL
          Meteo e temperatura -> Api pubbliche di www.wunderground.com
          ");

				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";crediti sent;" .$chat_id. "\n";
			}
			//richiede la temperatura
			elseif ($text == "/temperatura" || $text == "temperatura") {

	 			$log=$today. ";temp requested;" .$chat_id. "\n";
				$this->create_keyboard_temp($telegram,$chat_id);
				exit;
			}
			elseif ($text =="Lecce" || $text == "/temp-lecce")
			{
				 $reply = "Temperatura misurata in zona Lecce centro : " .$data->get_temperature("Lecce centro");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Lecce sent;" .$chat_id. "\n";
			}
			elseif ($text =="Nardò" || $text == "/temp-vaianosofignano")
			{
				 $reply = "Temperatura misurata in zona Nardò : " .$data->get_temperature("Nardò");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Nardò sent;" .$chat_id. "\n";
			}
			elseif ($text =="Lequile" || $text == "/temp-vaianoschignano")
			{
				 $reply = "Temperatura misurata in zona Lequile : " .$data->get_temperature("Lequile");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Lequile sent;" .$chat_id. "\n";
			}
			elseif ($text =="Galatina" || $text == "/temp-montepianovernio")
			{
				 $reply = "Temperatura misurata in zona Galatina : " .$data->get_temperature("Galatina");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Galatina sent;" .$chat_id. "\n";

			}

			elseif ($text=="notifiche on" || $text =="/on")
			{
				//abilita disabilita le notifiche automatiche del servizio
				//memorizza lo user_id
            	$statement = "INSERT INTO " . DB_TABLE ." (user_id) VALUES ('" . $user_id . "')";
            	$db->exec($statement);
				$reply = "Notifiche da openDataLecceBot abilitate. Per disabilitarle digita /off";
				$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);
				$log=$today. ";notification set;" .$chat_id. "\n";
			}
			elseif ($text=="notifiche off" || $text =="/off")
			{
				//abilita disabilita le notifiche automatiche del servizio
				//memorizza lo user_id
            	$statement = "DELETE FROM ". DB_TABLE ." where user_id = '" . $user_id . "'";
            	$db->exec($statement);
				$reply = "Notifiche da openDataLecceBot disabilitate. Per abilitarle digita /on";
				$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);
				$log=$today. ";notification reset;" .$chat_id. "\n";
			}

			//----- gestione segnalazioni georiferite : togliere per non gestire le segnalazioni georiferite -----
			elseif($location!=null)
			{

          $this->location_manager($db,$telegram,$user_id,$chat_id,$location);
          exit;

			}

			elseif($reply_to_msg!=null)
			{
				//inserisce la segnalazione nel DB delle segnalazioni georiferite

        $response=$telegram->getData();



    $type=$response["message"]["video"]["file_id"];
    $text =$response["message"] ["text"];
    $risposta="";
    $file_name="";
    $file_path="";
    $file_name="";

    if ($type !=NULL) {
    $file_id=$type;
    $text="video allegato";
    $risposta="ID dell'allegato:".$file_id;
    }

    $file_id=$response["message"]["photo"][0]["file_id"];

    if ($file_id !=NULL) {

    $telegramtk=TELEGRAM_BOT; // inserire il token
    $rawData = file_get_contents("https://api.telegram.org/bot".$telegramtk."/getFile?file_id=".$file_id);
    $obj=json_decode($rawData, true);
    $file_path=$obj["result"]["file_path"];
    $caption=$response["message"]["caption"];
    if ($caption != NULL) $text=$caption;
    $risposta="ID dell'allegato: ".$file_id;

    }
    $typed=$response["message"]["document"]["file_id"];

    if ($typed !=NULL){
    $file_id=$typed;
    $file_name=$response["message"]["document"]["file_name"];
    $text="documento: ".$file_name." allegato";
    $risposta="ID dell'allegato:".$file_id;

    }

    $typev=$response["message"]["voice"]["file_id"];
    if ($typev !=NULL){
    $file_id=$typev;
    $text="audio allegato";
    $risposta="ID dell'allegato:".$file_id;

    }
    $csv_path=dirname(__FILE__).'/./map_data.csv';
    $db_path=dirname(__FILE__).'/./lecceod.sqlite';
    $username=$response["message"]["from"]["username"];
    $first_name=$response["message"]["from"]["first_name"];

    $db1 = new SQLite3($db_path);
    $q = "SELECT lat,lng FROM ".DB_TABLE_GEO ." WHERE bot_request_message='".$reply_to_msg['message_id']."'";
    $result=	$db1->query($q);
    $row = array();
    $i=0;

    while($res = $result->fetchArray(SQLITE3_ASSOC)){

    						if(!isset($res['lat'])) continue;

    						 $row[$i]['lat'] = $res['lat'];
    						 $row[$i]['lng'] = $res['lng'];
    						 $i++;
    				 }

    		//inserisce la segnalazione nel DB delle segnalazioni georiferite
    			$statement = "UPDATE ".DB_TABLE_GEO ." SET text='".$text."',file_id='". $file_id ."',filename='". $file_name ."',first_name='". $first_name ."',file_path='". $file_path ."',username='". $username ."' WHERE bot_request_message ='".$reply_to_msg['message_id']."'";
    			print_r($reply_to_msg['message_id']);
    			$db->exec($statement);
    	//		$this->create_keyboard_temp($telegram,$chat_id);

    if ($text=="benzine" || $text=="farmacie" || $text=="musei")
    {
    	$tag="amenity=pharmacy";
    if ($text=="musei") $tag="tourism=museum";
    if ($text=="benzine") $tag="amenity=fuel";

    	      $lon=$row[0]['lng'];
    				$lat=$row[0]['lat'];
    	//prelevo dati da OSM sulla base della mia posizione
    					$osm_data=give_osm_data($lat,$lon,$tag	);

    					//rispondo inviando i dati di Openstreetmap
    					$osm_data_dec = simplexml_load_string($osm_data);

    					//per ogni nodo prelevo coordinate e nome
    					foreach ($osm_data_dec->node as $osm_element) {

    						$nome="";
    						foreach ($osm_element->tag as $key) {
    print_r($key);
    							if ($key['k']=='name' || $key['k']=='wheelchair' || $key['k']=='phone' || $key['k']=='addr:street' )
    							{
    							if ($key['k']=='wheelchair')
    									{
    											$valore=utf8_encode($key['v'])."\n";
    											$valore=str_replace("yes","si",$valore);
    											$valore=str_replace("limited","con limitazioni",$valore);
    											$nome .="Accessibile da disabili: ".$valore;
    									}
    							if ($key['k']=='phone')	$nome  .="Telefono: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='addr:street')	$nome .="Indirizzo: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='name')	$nome  .="Nome: ".utf8_encode($key['v'])."\n";

    							}

    						}
    						//gestione musei senza il tag nome
    						if($nome=="")
    						{
    							//	$nome=utf8_encode("Luogo non presente o identificato su Openstreetmap");
    							//	$content = array('chat_id' => $chat_id, 'text' =>$nome);
    							//	$telegram->sendMessage($content);
    						}
    						$content = array('chat_id' => $chat_id, 'text' =>$nome);
    						$telegram->sendMessage($content);
    						$reply = "Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$osm_element['lat']."&mlon=".$osm_element['lon']."#map=19/".$osm_element['lat']."/".$osm_element['lon'];
    						$content = array('chat_id' => $chat_id, 'text' => $reply);
    						$telegram->sendMessage($content);
    					 }

    					//crediti dei dati
    					if((bool)$osm_data_dec->node)
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode("Questi sono i luoghi vicini a te entro 5km \n(dati forniti tramite OpenStreetMap. Licenza ODbL (c) OpenStreetMap contributors)"));
    						$bot_request_message=$telegram->sendMessage($content);
    					}else
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode("Non ci sono sono luoghi vicini, mi spiace! Se ne conosci uno nelle vicinanze mappalo su www.openstreetmap.org"));
    						$bot_request_message=$telegram->sendMessage($content);
    					}
    }else{


    			$reply = "La segnalazione è stata Registrata.\n".$risposta."\nGrazie! ";
          // creare una mappa su umap, mettere nel layer -> dati remoti -> il link al file map_data.csv
    			$reply .= "Puoi visualizzarla su :\nhttp://umap.openstreetmap.fr/it/map/segnalazioni-con-opendataleccebot-x-interni_54105#19/".$row[0]['lat']."/".$row[0]['lng'];
    			$content = array('chat_id' => $chat_id, 'text' => $reply);
    			$telegram->sendMessage($content);
    			$log=$today. ";information for maps recorded;" .$chat_id. "\n";

    			exec(' sqlite3 -header -csv '.$db_path.' "select * from segnalazioni;" > '.$csv_path. ' ');
    }

    		}
			//comando errato
			else{
				 $reply = "Hai selezionato un comando non previsto";
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";wrong command sent;" .$chat_id. "\n";
			 }

			//gestione messaggi in broadcast : al momento gestisce il database per iscrizione delle notifiche automatiche ma non invia nessuna notifica
			//da commentare per disabilitare la gestione delle notifiche automatiche
		//  	$this->broadcast_manager($db,$telegram);



			//aggiorna tastiera
			$this->create_keyboard($telegram,$chat_id);

			//log
			file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);

			//db
		//	$statement = "INSERT INTO " . DB_TABLE_LOG ." (date, text, chat_id, user_id, location, reply_to_msg) VALUES ('" . $today . "','" . $text . "','" . $chat_id . "','" . $user_id . "','" . $location . "','" . $reply_to_msg . "')";
    //        $db->exec($statement);

	}


	// Crea la tastiera
	 function create_keyboard($telegram, $chat_id)
		{
				$option = array(["meteo oggi","previsioni"],["bollettini rischi","temperatura"],["Lecce Events","qualità aria"],["informazioni","traffico"]);
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[seleziona un'etichetta oppure clicca sulla graffetta e poi *posizione* per segnalarci qualcosa. Aggiornamento risposte ogni minuto]");
				$telegram->sendMessage($content);
		}

	//crea la tastiera per scegliere la zona temperatura
	 function create_keyboard_temp($telegram, $chat_id)
		{
				$option = array(["Lecce","Lequile"],["Nardò", "Galatina"]);
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Seleziona la località. Aggiornamento risposte ogni minuto]");
				$telegram->sendMessage($content);
		}

	//controlla le condizioni per gestire le notifiche automatiche
	function broadcast_manager($db,$telegram)
		{
			//gestione allarmi da completare.
			if(check_alarm())
			{
				sendMessagetoAll($db,$telegram,'message','Prova messaggio broadcast');
			}
		}



  function location_manager($db,$telegram,$user_id,$chat_id,$location)
  	{
  			$lng=$location["longitude"];
  			$lat=$location["latitude"];

  			//rispondo
  			$response=$telegram->getData();
  			$bot_request_message_id=$response["message"]["message_id"];
  			$time=$response["message"]["date"]; //registro nel DB anche il tempo unix

  			$h = "2";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
  			$hm = $h * 60;
  			$ms = $hm * 60;
  			$timec=gmdate("Y-m-d\TH:i:s\Z", $time+($ms));
  			$timec=str_replace("T"," ",$timec);
  			$timec=str_replace("Z"," ",$timec);
  			//nascondo la tastiera e forzo l'utente a darmi una risposta
  			$forcehide=$telegram->buildForceReply(true);

  			//chiedo cosa sta accadendo nel luogo
//  			$content = array('chat_id' => $chat_id, 'text' => "[Scrivici cosa sta accadendo qui]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

        $content = array('chat_id' => $chat_id, 'text' => "[Cosa vuole comunicarci su questo posto? oppure, in via sperimentale, scriva:\n\nfarmacie\no\nmusei\no\nbenzine (tutto minuscolo).\n\nLe indicheremo quelle più vicine nell'arco di 5km]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

        $bot_request_message=$telegram->sendMessage($content);

  			//memorizzare nel DB
  			$obj=json_decode($bot_request_message);
  			$id=$obj->result;
  			$id=$id->message_id;

  			//print_r($id);
  			$statement = "INSERT INTO ". DB_TABLE_GEO. " (lat,lng,user,username,text,bot_request_message,time,file_id,file_path,filename,first_name) VALUES ('" . $lat . "','" . $lng . "','" . $user_id . "',' ',' ','". $id ."','". $timec ."',' ',' ',' ',' ')";
  						$db->exec($statement);
  	}


  }

  ?>