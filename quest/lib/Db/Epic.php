<?php

namespace OCA\NextcloudQuest\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method ?string getDescription()
 * @method void setDescription(?string $description)
 * @method ?string getEmoji()
 * @method void setEmoji(?string $emoji)
 * @method ?string getColor()
 * @method void setColor(?string $color)
 * @method string getTier()
 * @method void setTier(string $tier)
 * @method string getStatus()
 * @method void setStatus(string $status)
 * @method int getTotalTasks()
 * @method void setTotalTasks(int $totalTasks)
 * @method int getCompletedTasks()
 * @method void setCompletedTasks(int $completedTasks)
 * @method int getTotalXpEarned()
 * @method void setTotalXpEarned(int $totalXpEarned)
 * @method int getBonusXpAwarded()
 * @method void setBonusXpAwarded(int $bonusXpAwarded)
 * @method ?string getCompletedAt()
 * @method void setCompletedAt(?string $completedAt)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $createdAt)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $updatedAt)
 */
class Epic extends Entity implements JsonSerializable {
    protected string $userId = '';
    protected string $title = '';
    protected ?string $description = null;
    protected ?string $emoji = null;
    protected ?string $color = null;
    protected string $tier = 'common';
    protected string $status = 'active';
    protected int $totalTasks = 0;
    protected int $completedTasks = 0;
    protected int $totalXpEarned = 0;
    protected int $bonusXpAwarded = 0;
    protected ?string $completedAt = null;
    protected string $createdAt = '';
    protected string $updatedAt = '';

    public function __construct() {
        $this->addType('id', 'integer');
        $this->addType('totalTasks', 'integer');
        $this->addType('completedTasks', 'integer');
        $this->addType('totalXpEarned', 'integer');
        $this->addType('bonusXpAwarded', 'integer');
    }

    public function getProgressPercentage(): float {
        if ($this->totalTasks === 0) return 0;
        return round(($this->completedTasks / $this->totalTasks) * 100, 1);
    }

    public function isCompleted(): bool {
        return $this->status === 'completed';
    }

    public static function tierFromTaskCount(int $count): string {
        if ($count >= 21) return 'legendary';
        if ($count >= 13) return 'epic';
        if ($count >= 8) return 'rare';
        if ($count >= 4) return 'uncommon';
        return 'common';
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'color' => $this->color,
            'tier' => $this->tier,
            'status' => $this->status,
            'total_tasks' => $this->totalTasks,
            'completed_tasks' => $this->completedTasks,
            'progress_percentage' => $this->getProgressPercentage(),
            'total_xp_earned' => $this->totalXpEarned,
            'bonus_xp_awarded' => $this->bonusXpAwarded,
            'completed_at' => $this->completedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
