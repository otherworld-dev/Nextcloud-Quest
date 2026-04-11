<?php
/**
 * @copyright Copyright (c) 2025 Quest Team
 *
 * @license GNU AGPL version 3 or any later version
 */

namespace OCA\NextcloudQuest\Controller\Base;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IInitialStateService;
use OCP\IUserSession;
use OCP\L10N\IFactory as IL10NFactory;
use OCP\Util;

abstract class BasePageController extends Controller {
    /** @var IInitialStateService */
    protected $initialStateService;
    /** @var IUserSession */
    protected $userSession;
    /** @var IL10NFactory */
    protected $l10nFactory;

    public function __construct(
        $appName,
        IRequest $request,
        IInitialStateService $initialStateService,
        IUserSession $userSession,
        IL10NFactory $l10nFactory
    ) {
        parent::__construct($appName, $request);
        $this->initialStateService = $initialStateService;
        $this->userSession = $userSession;
        $this->l10nFactory = $l10nFactory;
    }

    /**
     * Render a page with common initialization.
     * All rendering is handled by Vue — we only load the webpack bundle and design tokens.
     */
    protected function renderPage(string $pageName, string $templateName, array $additionalScripts = [], array $additionalStyles = []): TemplateResponse {
        $user = $this->userSession->getUser();

        // Design tokens (CSS custom properties)
        Util::addStyle('quest', 'base/variables');

        // Vue app bundle (single entry point — handles all pages)
        Util::addScript('quest', 'nextcloud-quest-main');

        // Provide user state to Vue via loadState()
        $this->initialStateService->provideInitialState(
            'quest',
            'user',
            [
                'uid' => $user->getUID(),
                'displayName' => $user->getDisplayName()
            ]
        );

        // Provide page config so Vue knows which page to render
        $this->initialStateService->provideInitialState(
            'quest',
            'config',
            [
                'active_page' => $pageName,
                'language' => $this->l10nFactory->get('quest')->getLanguageCode()
            ]
        );

        return new TemplateResponse('quest', $templateName, [
            'active_page' => $pageName,
        ]);
    }
}
