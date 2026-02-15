<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_id',
        'reference_type',
        'link',
        'is_read',
        'read_at',
        'created_at'
    ];

    protected $returnType = 'array';

    /**
     * Get unread notifications for user
     */
    public function getUnreadForUser($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->orWhere('user_id IS NULL') // System notifications
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get all notifications for user
     */
    public function getAllForUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orWhere('user_id IS NULL')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Count unread notifications
     */
    public function countUnread($userId)
    {
        return $this->where('user_id', $userId)
            ->orWhere('user_id IS NULL')
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Create notification
     */
    public function createNotification($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /**
     * Clean old notifications (keep last 30 days)
     */
    public function cleanOldNotifications()
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->delete();
    }

    /**
     * Get user notification settings
     */
    public function getUserSettings($userId)
    {
        // Get from database or return defaults
        $settings = $this->db->table('notification_settings')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$settings) {
            // Return default settings
            return [
                'low_stock' => true,
                'overdue_receivable' => true,
                'overdue_payable' => true,
                'pending_po' => true,
                'daily_report' => false,
                'email_notifications' => false
            ];
        }

        return $settings;
    }

    /**
     * Update user notification settings
     */
    public function updateUserSettings($userId, $settings)
    {
        $existing = $this->db->table('notification_settings')
            ->where('user_id', $userId)
            ->get()
            ->getRow();

        if ($existing) {
            return $this->db->table('notification_settings')
                ->where('user_id', $userId)
                ->update($settings);
        } else {
            $settings['user_id'] = $userId;
            return $this->db->table('notification_settings')
                ->insert($settings);
        }
    }
}
