<?php

namespace OCA\NextcloudQuest\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method int getEpicId()
 * @method void setEpicId(int $epicId)
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getTaskUid()
 * @method void setTaskUid(string $taskUid)
 * @method string getListId()
 * @method void setListId(string $listId)
 * @method ?string getTaskTitle()
 * @method void setTaskTitle(?string $taskTitle)
 * @method int getXpEarned()
 * @method void setXpEarned(int $xpEarned)
 * @method int getIsCompleted()
 * @method void setIsCompleted(int $isCompleted)
 * @method ?string getCompletedAt()
 * @method void setCompletedAt(?string $completedAt)
 * @method string getAddedAt()
 * @method void setAddedAt(string $addedAt)
 */
class EpicTask extends Entity implements JsonSerializable {
    protected int $epicId = 0;
    protected string $userId = '';
    protected string $taskUid = '';
    protected string $listId = '';
    protected ?string $taskTitle = null;
    protected int $xpEarned = 0;
    protected int $isCompleted = 0;
    protected ?string $completedAt = null;
    protected string $addedAt = '';

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('epicId', 'integer');
        $this->addType('xpEarned', 'integer');
        $this->addType('isCompleted', 'integer');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'epic_id' => $this->epicId,
            'task_uid' => $this->taskUid,
            'list_id' => $this->listId,
            'task_title' => $this->taskTitle,
            'xp_earned' => $this->xpEarned,
            'is_completed' => $this->isCompleted,
            'completed_at' => $this->completedAt,
            'added_at' => $this->addedAt,
        ];
    }
}
