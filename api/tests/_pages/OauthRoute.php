<?php
declare(strict_types=1);

namespace api\tests\_pages;

/**
 * @deprecated
 * TODO: remove
 */
class OauthRoute extends BasePage {

    /**
     * @deprecated
     */
    public function createClient(string $type, array $postParams): void {
        $this->getActor()->sendPOST('/api/v1/oauth2/' . $type, $postParams);
    }

    /**
     * @deprecated
     */
    public function updateClient(string $clientId, array $params): void {
        $this->getActor()->sendPUT('/api/v1/oauth2/' . $clientId, $params);
    }

    /**
     * @deprecated
     */
    public function deleteClient(string $clientId): void {
        $this->getActor()->sendDELETE('/api/v1/oauth2/' . $clientId);
    }

    /**
     * @deprecated
     */
    public function resetClient(string $clientId, bool $regenerateSecret = false): void {
        $this->getActor()->sendPOST("/api/v1/oauth2/{$clientId}/reset" . ($regenerateSecret ? '?regenerateSecret' : ''));
    }

    /**
     * @deprecated
     */
    public function getClient(string $clientId): void {
        $this->getActor()->sendGET("/api/v1/oauth2/{$clientId}");
    }

    /**
     * @deprecated
     */
    public function getPerAccount(int $accountId): void {
        $this->getActor()->sendGET("/api/v1/accounts/{$accountId}/oauth2/clients");
    }

}
