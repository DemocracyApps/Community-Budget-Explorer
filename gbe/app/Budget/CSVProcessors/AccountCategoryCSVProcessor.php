<?php namespace DemocracyApps\GB\Budget\CSVProcessors;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */
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