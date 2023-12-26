<?php

/*
    Dev Tools and Functions
    Please don't touch

    by C.
    09/05/2023
*/

namespace DevTools {

    use PDO;

    require_once __DIR__ . '/../@/config.php';
    require_once __DIR__ . '/../@/init.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    define("WEBHOOK_URL", "https://discord.com/api/webhooks/1105571504973946951/WhpycDGHH38gL7cJ5ZtPBvE7Jf8WBbTviuZ74eckGzaNpyOQATbMglpsQuptQ7B2uG_M");

    if ($odb == null || !($odb instanceof PDO)) {
        die("Database connection failed.");
    }

    $GLOBALS["database"] = $odb;

    class AttackLogger {
        private string $webhookUrl;

        private function sendWebhookData(array $webhookData, $callback) {
            $jsonData = json_encode($webhookData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $curlRequest = curl_init($this->webhookUrl);
            curl_setopt($curlRequest, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($curlRequest, CURLOPT_POST, 1);
            curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($curlRequest, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlRequest, CURLOPT_HEADER, 0);
            curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($curlRequest);
            //var_dump($response);
            curl_close($curlRequest);

            if ($callback) {
                $callback();
            }
        }

        function record(AttackData $attackData) {
            $this->sendWebhookData([
                "embeds" => [
                    [
                        "title" => "Logs",
                        "description" => "Recorded a new attack !\n**Request Type :** {$attackData->type}",
                        "color" => 5814783,
                        "fields" => [
                            [
                                "name" => "User Infos",
                                "value" => "- **Name :** {$attackData->username}\n- **ID :** {$attackData->id}\n- **Admin :** {$attackData->admin}\n- **VIP :** {$attackData->vip}\n- **Premium :** {$attackData->premium}\n- **Supporter :** {$attackData->supporter}"
                            ],
                            [
                                "name" => "Attack Infos",
                                "value" => "- **Host :** {$attackData->host}\n- **Port :** {$attackData->port}\n- **Boot Time :** {$attackData->bootTime}\n- **Method :** {$attackData->method}\n- **Concurrents :** {$attackData->concurrents}"
                            ]
                        ]
                    ]
                ],
                "username" => "HexStresser",
                "avatar_url" => "https://zupimages.net/up/23/07/s2cy.gif"
            ], null);
        }

        function setWebhookUrl(string $url) {
            $this->webhookUrl = $url;
        }
    }
    
    class AttackData {
        public string $username;
        public string $admin;
        public string $vip;
        public string $premium;
        public string $supporter;
        public string $host;
        public string $port;
        public string $bootTime;
        public string $method;
        public string $type;
        public string $concurrents;

        public string $id;

        function setUser(\user $user) {
            $this->setAdmin($user->isAdmin($GLOBALS["database"]));
            $this->setVIP($user->isVIP($GLOBALS["database"]));
            $this->setPremium($user->isPremium($GLOBALS["database"]));
            $this->setSupporter($user->isSupporter($GLOBALS["database"]));
        }

        function setUsername(string $username) {
            $this->username = $username;
        }

        function setAdmin(bool $admin) {
            $this->admin = $admin ? "             :white_check_mark:" : "             :x:";
        }

        function setVIP(bool $vip) {
            $this->vip = $vip ? "                   :white_check_mark:" : "                   :x:";
        }

        function setPremium(bool $premium) {
            $this->premium = $premium ? "        :white_check_mark:" : "        :x:";
        }

        function setSupporter(bool $supporter) {
            $this->supporter = $supporter ? "      :white_check_mark:" : "       :x:";
        }

        function setHost(string $host) {
            $this->host = "``$host``";
        }

        function setPort(int $port) {
            $this->port = "``" . strval($port) . "``";
        }

        function setBootTime(int $bootTime) {
            $this->bootTime = "``" . strval($bootTime) . "``";
        }

        function setMethod(string $method) {
            $this->method = "``$method``";
        }

        function setType($type) {
            if (!is_null($type)) {
                $this->type = $type;
            }
        }

        function setConcurrents($concurrents) {
            $this->concurrents = "``$concurrents``";
        }

        function setId($id) {
            $this->id = "``" . strval($id) . "``";
        }
    }
}
