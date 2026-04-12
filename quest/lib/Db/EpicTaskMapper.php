<?php

namespace OCA\NextcloudQuest\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class EpicTaskMapper extends QBMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'ncquest_epic_tasks', EpicTask::class);
    }

    public function findByEpicId(int $epicId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('epic_id', $qb->createNamedParameter($epicId, \PDO::PARAM_INT)))
            ->orderBy('added_at', 'ASC');
        return $this->findEntities($qb);
    }

    public function findByTaskUid(string $taskUid, string $listId, string $userId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('task_uid', $qb->createNamedParameter($taskUid)))
            ->andWhere($qb->expr()->eq('list_id', $qb->createNamedParameter($listId)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
        return $this->findEntities($qb);
    }

    public function findIncompleteByTaskUid(string $taskUid, string $listId, string $userId): array {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where($qb->expr()->eq('task_uid', $qb->createNamedParameter($taskUid)))
            ->andWhere($qb->expr()->eq('list_id', $qb->createNamedParameter($listId)))
            ->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
            ->andWhere($qb->expr()->eq('is_completed', $qb->createNamedParameter(0, \PDO::PARAM_INT)));
        return $this->findEntities($qb);
    }

    public function deleteByEpicId(int $epicId): void {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where($qb->expr()->eq('epic_id', $qb->createNamedParameter($epicId, \PDO::PARAM_INT)));
        $qb->executeStatement();
    }
}
