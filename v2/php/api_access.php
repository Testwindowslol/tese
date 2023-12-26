<?php

namespace APIAccess {
    include_once __DIR__ . "/config.php";

    function hasApiAccess($userID) : bool {
        global $db;

        $stmt = $db->prepare("SELECT plans.premium FROM users JOIN plans ON users.membership = plans.ID WHERE users.ID = ?");
        $stmt->execute([
           $userID
        ]);

        if ($stmt->rowCount() == 0) {
            return false;
        }

        $premium = $stmt->fetch(\PDO::FETCH_ASSOC)["premium"];

        return $premium >= 3;
    }

    function getApiKey($userID) : string {
        global $db;

        $stmt = $db->prepare("SELECT id, secret FROM api_keys WHERE user_id = ? AND active = true");
        $stmt->execute([
            $userID
        ]);

        if ($stmt->rowCount() == 0) {
            return generateApiKey($userID);
        }

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $id = $data["id"];

        $secret = $data["secret"];
        $userHash = substr(sha1("user:" . $userID), 0, 16);
        $keyHash = substr(sha1("key:" . $id), 0, 16);

        return $userHash . "." . $keyHash . "." . $secret;
    }

    function generateApiKey($userID) : string {
        global $db;

        $stmt = $db->prepare("SELECT secret FROM api_keys WHERE user_id = ?");
        $stmt->execute([
            $userID
        ]);

        if ($stmt->rowCount() > 0) {
            // Disable old api keys
            $stmt = $db->prepare("UPDATE api_keys SET active = false WHERE user_id = ?");
            $stmt->execute([
                $userID
            ]);
        }

        // Generate a secret
        try {
            $secret = sha1("secret:" . $userID . "." . random_bytes(12));
        } catch (\Exception $exception) {
            die("Unable to generate a secure API Key.");
        }

        // Insert the api key into the database (we only need to insert the secret)
        $stmt = $db->prepare("INSERT INTO api_keys(user_id, secret, active) VALUES(?, ?, true)");
        $stmt->execute([
           $userID,
           $secret
        ]);

        // Get the inserted api key id
        $stmt = $db->query("SELECT MAX(id) AS id FROM api_keys");
        $id = $stmt->fetch(\PDO::FETCH_ASSOC)["id"];

        $userHash = substr(sha1("user:" . $userID), 0, 16);
        $keyHash = substr(sha1("key:" . $id), 0, 16);

        return $userHash . "." . $keyHash . "." . $secret;
    }

    function isApiKeyValid($apiKey) : bool {
        global $db;

        if (strlen($apiKey) != 74) {
            return false;
        }

        $apiKeyData = explode(".", $apiKey);
        if (count($apiKeyData) != 3) {
            return false;
        }

        $userHash = $apiKeyData[0];
        $keyHash = $apiKeyData[1];
        $secret = $apiKeyData[2];

        if (strlen($userHash) != 16) {
            return false;
        }
        if (strlen($keyHash) != 16) {
            return false;
        }
        if (strlen($secret) != 40) {
            return false;
        }

        $stmt = $db->prepare("SELECT id, user_id, active FROM api_keys WHERE secret = ?");
        $stmt->execute([
            $secret
        ]);

        if ($stmt->rowCount() == 0) {
            return false;
        }

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $id = $data["id"];
        $userID = $data["user_id"];
        $active = $data["active"];

        if (!$active) {
            return false;
        }

        if ($userHash != substr(sha1("user:" . $userID), 0, 16)) {
            return false;
        }
        if ($keyHash != substr(sha1("key:" . $id), 0, 16)) {
            return false;
        }

        return true;
    }

    function getApiKeyData($apiKey) : array {
        global $db;

        if (!isApiKeyValid($apiKey)) {
            return [];
        }

        $apiKeyData = explode(".", $apiKey);
        $secret = $apiKeyData[2];

        $stmt = $db->prepare("SELECT id, user_id, active FROM api_keys WHERE secret = ?");
        $stmt->execute([
            $secret
        ]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        $id = $data["id"];
        $userID = $data["user_id"];
        $active = $data["active"];

        return [
            "id" => $id,
            "user_id" => $userID,
            "secret" => $secret,
            "active" => $active // true...
        ];
    }
}