<?php namespace DemocracyApps\GB\Accounts\CSVProcessors;

use DemocracyApps\GB\Accounts\Dataset;
use DemocracyApps\GB\Utility\Notification;

class DatasetCSVProcessor
{

    public function fire($queueJob, $data)
    {
        $userId = $data['userId'];
        $user = \DemocracyApps\GB\Users\User::findOrFail($userId);
        \Auth::login($user);

        $filePath = $data['filePath'];
        $dataset = $data['dataset'];


        \Log::info("Starting processing of " . $filePath);
        $notification = Notification::find($data['notificationId']);
        $notification->messages = Dataset::processCsvInput($filePath, $dataset);
        $notification->status = 'Completed';
        $notification->completed_at = date('Y-m-d H:i:s');
        $notification->save();
        \Log::info("Completed processing of job " . $notification->id . " for " . $filePath);

        unlink($filePath);
        $queueJob->delete();
    }

}