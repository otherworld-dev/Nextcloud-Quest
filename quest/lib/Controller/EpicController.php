<?php

namespace OCA\NextcloudQuest\Controller;

use OCA\NextcloudQuest\Service\EpicService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class EpicController extends Controller {
    private IUserSession $userSession;
    private EpicService $epicService;

    public function __construct($appName, IRequest $request, IUserSession $userSession, EpicService $epicService) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->epicService = $epicService;
    }

    private function getUserId(): string {
        return $this->userSession->getUser()->getUID();
    }

    private function getInput(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getEpics(): JSONResponse {
        try {
            return new JSONResponse([
                'status' => 'success',
                'data' => $this->epicService->getEpics($this->getUserId()),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getEpic(int $id): JSONResponse {
        try {
            return new JSONResponse([
                'status' => 'success',
                'data' => $this->epicService->getEpic($id, $this->getUserId()),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function createEpic(): JSONResponse {
        try {
            $input = $this->getInput();
            $epic = $this->epicService->createEpic(
                $this->getUserId(),
                $input['title'] ?? 'Untitled Epic',
                $input['description'] ?? null,
                $input['emoji'] ?? null,
                $input['color'] ?? null
            );
            return new JSONResponse([
                'status' => 'success',
                'data' => $epic->jsonSerialize(),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function updateEpic(int $id): JSONResponse {
        try {
            $epic = $this->epicService->updateEpic($id, $this->getUserId(), $this->getInput());
            return new JSONResponse([
                'status' => 'success',
                'data' => $epic->jsonSerialize(),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function deleteEpic(int $id): JSONResponse {
        try {
            $this->epicService->deleteEpic($id, $this->getUserId());
            return new JSONResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function addTask(int $id): JSONResponse {
        try {
            $input = $this->getInput();
            $task = $this->epicService->addTask(
                $id,
                $this->getUserId(),
                (string)($input['task_uid'] ?? ''),
                (string)($input['list_id'] ?? ''),
                $input['task_title'] ?? null
            );
            return new JSONResponse([
                'status' => 'success',
                'data' => $task->jsonSerialize(),
            ]);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function removeTask(int $id): JSONResponse {
        try {
            $input = $this->getInput();
            $this->epicService->removeTask(
                $id,
                $this->getUserId(),
                (string)($input['task_uid'] ?? ''),
                (string)($input['list_id'] ?? '')
            );
            return new JSONResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
