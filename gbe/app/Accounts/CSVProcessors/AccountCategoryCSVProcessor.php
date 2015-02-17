<?php namespace DemocracyApps\GB\Accounts\CSVProcessors;

use DemocracyApps\GB\Accounts\AccountCategory;
use DemocracyApps\GB\Utility\Notification;

class AccountCategoryCSVProcessor
{

    public function fire($queueJob, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\GB\Users\User::findOrFail($userId);
        \Auth::login($user);

        $filePath = $data['filePath'];
        $category = $data['category'];


        \Log::info("Starting processing of " . $filePath);
        $notification = Notification::find($data['notificationId']);
        $notification->messages = AccountCategory::processCsvInput($filePath, $category);
        $notification->status = 'Completed';
        $notification->completed_at = date('Y-m-d H:i:s');
        $notification->save();
        \Log::info("Completed processing of job " . $notification->id . " for " . $filePath);

        unlink($filePath);
        $queueJob->delete();
    }

}