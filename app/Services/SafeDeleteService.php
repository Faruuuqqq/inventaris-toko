<?php

namespace App\Services;

use CodeIgniter\Model;

/**
 * Safe Delete Service
 * 
 * Provides safe deletion with validation to prevent cascade delete issues
 */
class SafeDeleteService
{
    /**
     * Check if a record can be safely deleted
     * 
     * @param Model $model
     * @param string $parentId
     * @param array $childConfigs Array of ['table' => 'table_name', 'fk' => 'foreign_key_field']
     * @return array ['canDelete' => bool, 'issues' => array]
     */
    public static function checkDeletionSafety($parentId, $childConfigs = [])
    {
        $db = \Config\Database::connect();
        $issues = [];

        foreach ($childConfigs as $config) {
            $table = $config['table'];
            $fk = $config['fk'];
            $description = $config['description'] ?? $table;

            // Count child records
            $result = $db->query(
                "SELECT COUNT(*) as count FROM {$table} WHERE {$fk} = ? AND deleted_at IS NULL",
                [$parentId]
            )->getRow();

            if ($result->count > 0) {
                $issues[] = [
                    'table' => $table,
                    'count' => $result->count,
                    'description' => $description,
                    'message' => "Tidak bisa menghapus karena memiliki {$result->count} {$description}"
                ];
            }
        }

        return [
            'canDelete' => empty($issues),
            'issues' => $issues
        ];
    }

    /**
     * Get child record details before deletion
     */
    public static function getChildDetails($parentId, $config)
    {
        $db = \Config\Database::connect();

        return $db->query(
            "SELECT * FROM {$config['table']} WHERE {$config['fk']} = ? AND deleted_at IS NULL",
            [$parentId]
        )->getResultArray();
    }
}
