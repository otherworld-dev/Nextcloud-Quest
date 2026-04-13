<?php

namespace OCA\NextcloudQuest\Controller;

use OCA\NextcloudQuest\Service\ChallengeService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class ChallengeController extends Controller {
    private IUserSession $userSession;
    private ChallengeService $challengeService;

    public function __construct($appName, IRequest $request, IUserSession $userSession, ChallengeService $challengeService) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->challengeService = $challengeService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getChallenges(): JSONResponse {
        try {
            $userId = $this->userSession->getUser()->getUID();
            return new JSONResponse([
                'status' => 'success',
                'data' => $this->challengeService->getChallenges($userId),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
