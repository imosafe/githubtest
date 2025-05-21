<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class ApiWestcon {
    private $client_id;
    private $client_secret;
    private $grant_type;
    private $resource;
    private $partnerKey;
    private $subscriptionKey;
    private $token;
    private $token_expiry;

    public function __construct($client_id, $client_secret, $grant_type, $resource, $partnerKey, $subscriptionKey) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->grant_type = $grant_type;
        $this->resource = $resource;
        $this->partnerKey = $partnerKey;
        $this->subscriptionKey = $subscriptionKey;
        $this->token = $this->getToken();
    }

    // Fonction pour obtenir le token d'accès
    private function getToken() {
        if($this->token && $this->token_expiry && time() < $this->token_expiry){
            return $this->token;
        }
        if ($this->client_id && $this->client_secret && $this->grant_type && $this->resource) {
            $url = "https://login.microsoftonline.com/ec8933c6-cfb2-4dd9-bfc9-621cde1dea8f/oauth2/token";
            $body = array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => $this->grant_type,
                'resource' => $this->resource
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));

            $data = curl_exec($curl);
           
            if ($data==false) {
                echo 'Erreur CURL: ' . curl_error($curl);
                return null;
            } else {
                $result = json_decode($data, true);
                curl_close($curl);
                if(isset($result['access_token'])){
                    $this->token= $result['access_token'];
                    $this->token_expiry= time() + $result['expires_in'];
                    return $this->token;
                }else{
                    echo "Erreur lors de la récupération du token";
                    return null;
                }
            }
        } else {
            echo "Veuillez entrer les quatre paramètres";
            return null;
        }
    }

    // Fonction centralisée pour les requêtes CURL
    private function curlResponse($url, $body) {
        $curl = curl_init();
        $header = array(
            'Authorization: Bearer ' . $this->token,
            'Ocp-Apim-Subscription-key: ' . $this->subscriptionKey,
            'Content-Type: application/json'
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $data = curl_exec($curl);

        if ($data === false) {
            $error = curl_error($curl);
            echo 'Erreur CURL: ' . $error;
        } else {
            $response = json_decode($data, true);
            if ($response === null) {
                echo "Erreur de décodage JSON";
            } else {
                return $response;
            }
        }
        curl_close($curl);
    }

    // Fonction pour obtenir les informations de suivi du transporteur
    public function getCarrierTracking($orderNumber, $carrier, $trackingNumber) {
        if (!$this->token) {
            echo "Erreur lors de la récupération du token";
            return;
        }

        $body = json_encode(array(
            "shipmentTracking" => array(
                "mT_ShipmentTracking_API_Req" => array(
                    "partnerKey" => $this->partnerKey,
                    "orderNumber" => $orderNumber,
                    "carrier" => $carrier,
                    "trackingNumber" => $trackingNumber
                )
            )
        ));

        $url = "https://westconapiuat.azure-api.net/CarrierTracking/GetTrackingInfo";
        $response = $this->curlResponse($url, $body);
        if ($response != null) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }
    }

    // Fonction pour obtenir les détails d'une expédition
    public function getShipmentDetail($orderType, $language, $orderNumber, $trackingData, $serialData, $vrfData, $secondaryVRFReferenceField = null, $secondaryVRFReferenceValue = null) {
        if (!$this->token) {
            echo "Erreur lors de la récupération du token";
            return;
        }

        $bodyArray = array(
            "mT_OrderTrack_API_Req" => array(
                "partnerKey" => $this->partnerKey,
                "orderType" => $orderType,
                "language" => $language,
                "orderNumber" => $orderNumber,
                "trackingData" => $trackingData,
                "serialData" => $serialData,
                "vrfData" => $vrfData
            )
        );

        if ($secondaryVRFReferenceField !== null) {
            $bodyArray["mT_OrderTrack_API_Req"]["secondaryVRFReferenceField"] = $secondaryVRFReferenceField;
        }
        if ($secondaryVRFReferenceValue !== null) {
            $bodyArray["mT_OrderTrack_API_Req"]["secondaryVRFReferenceValue"] = $secondaryVRFReferenceValue;
        }

        $body = json_encode($bodyArray);
        $url = "https://westconapiuat.azure-api.net/shipping/ShipmentDetail";
        $response = $this->curlResponse($url, $body);
        if ($response != null) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }
    }

    // Fonction pour obtenir le statut d'une commande
    public function getOrderStatus($orderType, $language, $order, $orderNumber, $secondaryVRFReferenceField = null, $secondaryVRFReferenceValue = null) {
        if (!$this->token) {
            echo "Erreur lors de la récupération du token";
            return;
        }

        $bodyArray = array(
            "mT_OrderStatus_API_REQ" => array(
                "partnerKey" => $this->partnerKey,
                "orderType" => $orderType,
                "language" => $language,
                "order" => $order,
                "orderNumber" => $orderNumber
            )
        );

        if ($secondaryVRFReferenceField !== null) {
            $bodyArray["mT_OrderStatus_API_REQ"]["secondaryVRFReferenceField"] = $secondaryVRFReferenceField;
        }
        if ($secondaryVRFReferenceValue !== null) {
            $bodyArray["mT_OrderStatus_API_REQ"]["secondaryVRFReferenceValue"] = $secondaryVRFReferenceValue;
        }

        $body = json_encode($bodyArray);
        $url = "https://westconapiuat.azure-api.net/Orders/orderStatus";
        $response = $this->curlResponse($url, $body);
        if ($response != null) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }
    }

    // Fonction pour demander un devis
    public function getQuote($resellerId, $quoteId, $version) {
        if (!$this->token) {
            echo "Erreur lors de la récupération du token";
            return;
        }

        $body = json_encode(array(
            "partnerKey" => $this->partnerKey,
            "resellerId" => $resellerId,
            "quoteId" => $quoteId,
            "version" => $version
        ));

        $url = "https://westconapiuat.azure-api.net/QuoteDetail/retrieve";
        $response = $this->curlResponse($url, $body);
        if ($response != null) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }
    }

    // Fonction pour récupérer la liste des commandes en cours
    public function getOpenOrderList($startDate, $endDate) {
        if (!$this->token) {
            echo "Erreur lors de la récupération du token";
            return;
        }

        $body = json_encode(array(
            "mT_OpenOrderList_Req" => array(
                "partnerKey" => $this->partnerKey,
                "startDate" => $startDate,
                "endDate" => $endDate
            )
        ));

        $url = "https://westconapiuat.azure-api.net/OpenOrders/GetList";
        $response = $this->curlResponse($url, $body);
        if ($response != null) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }
    }
}

// Phase de test
$client_id = "3a3a4348-25b7-4daf-bad8-1ff365deb659";
$client_secret = "JJd8Q~AfEjgOr7XZsCLwuA91k8ID~kvszP9LbaK1";
$grant_type = "client_credentials";
$resource = "e46124f9-bbdf-4eb7-af1b-ef8f3e65c0e4";
$partnerKey = "005056A8F8441EDF8A9A94B5A5B04C6F";
$subscriptionKey = "a0232824a4bf49a0a05d05b61e532570";

// Appel de la classe ApiWestcon
$apiWestcon = new ApiWestcon($client_id, $client_secret, $grant_type, $resource, $partnerKey, $subscriptionKey);

$orderNumber = "963436322";
$carrier = "DHL";
$trackingNumber = "21219446317323412";
$orderType = "W";
$language = "EN";
$trackingData = "Y";
$serialData = "Y";
$vrfData = "Y";
$secondaryVRFReferenceField = "VRF";
$secondaryVRFReferenceValue = "0317";
$resellerId = "0000000000";
$quoteId = "00000001";
$version = "0";
$order = array("0000000001", "0000000002");
$startDate = "20240520";
$endDate = "20240525";

// Appel de la méthode pour obtenir les informations de suivi du transporteur
$apiWestcon->getCarrierTracking($orderNumber, $carrier, $trackingNumber);
// Appel de la méthode pour obtenir les détails de l'expédition
$apiWestcon->getShipmentDetail($orderType, $language, $orderNumber, $trackingData, $serialData, $vrfData, $secondaryVRFReferenceField, $secondaryVRFReferenceValue);
// Appel de la méthode pour obtenir le devis
$apiWestcon->getQuote($resellerId, $quoteId, $version);
// Appel de la méthode pour obtenir le statut d'une commande
$apiWestcon->getOrderStatus($orderType, $language, $order, $orderNumber, $secondaryVRFReferenceField, $secondaryVRFReferenceValue);
// Appel de la méthode pour récupérer la liste des commandes en cours
$apiWestcon->getOpenOrderList($startDate, $endDate);
?>
