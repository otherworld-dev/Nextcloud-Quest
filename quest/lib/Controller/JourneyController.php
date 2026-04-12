<?php

namespace OCA\NextcloudQuest\Controller;

use OCA\NextcloudQuest\Service\JourneyService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class JourneyController extends Controller {
    private IUserSession $userSession;
    private JourneyService $journeyService;

    public function __construct($appName, IRequest $request, IUserSession $userSession, JourneyService $journeyService) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->journeyService = $journeyService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getStatus(): JSONResponse {
        try {
            $userId = $this->userSession->getUser()->getUID();
            return new JSONResponse([
                'status' => 'success',
                'data' => $this->journeyService->getJourneyStatus($userId),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getLog(): JSONResponse {
        try {
            $userId = $this->userSession->getUser()->getUID();
            $limit = (int)($this->request->getParam('limit', 20));
            $offset = (int)($this->request->getParam('offset', 0));
            return new JSONResponse([
                'status' => 'success',
                'data' => $this->journeyService->getJourneyLog($userId, $limit, $offset),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
