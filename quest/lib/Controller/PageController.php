<?php
/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\NextcloudQuest\Controller;

use OCA\NextcloudQuest\Controller\Base\BasePageController;
use OCP\AppFramework\Http\TemplateResponse;

class PageController extends BasePageController {

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse {
        return $this->renderPage('dashboard', 'index');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function quests(): TemplateResponse {
        return $this->renderPage('quests', 'quests');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function achievements(): TemplateResponse {
        return $this->renderPage('achievements', 'achievements');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function adventure(): TemplateResponse {
        return $this->renderPage('adventure', 'adventure');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function character(): TemplateResponse {
        return $this->renderPage('character', 'character');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function activity(): TemplateResponse {
        return $this->renderPage('activity', 'index');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function leaderboard(): TemplateResponse {
        return $this->renderPage('leaderboard', 'index');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function settings(): TemplateResponse {
        return $this->renderPage('settings', 'settings');
    }
}
