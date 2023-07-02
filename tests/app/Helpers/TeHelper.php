<?php
namespace DTApi\Helpers;

use Carbon\Carbon;
use DTApi\Models\Job;
use DTApi\Models\User;
use DTApi\Models\Language;
use DTApi\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeHelper
{
    public static function fetchLanguageFromJobId($id)
    {
        $language = Language::findOrFail($id);
        return $language->language;
    }

    public static function getUsermeta($userId, $key = false)
    {
        $user = UserMeta::where('user_id', $userId)->first()->$key;
        if (!$key){
            return $user->usermeta()->get()->all();
        }
        else {
            $meta = $user->usermeta()->where('key', '=', $key)->get()->first();
            if ($meta){
                return $meta->value;
            }
            else{
                return '';
            }
        }
    }

    public static function convertJobIdsInObjs($jobsIds)
    {

        $jobs = array();
        foreach ($jobsIds as $job_obj) {
            $jobs[] = Job::findOrFail($job_obj->id);
        }
        return $jobs;
    }

    public static function willExpireAt($dueTime, $createdAt)
    {
        $dueTime = Carbon::parse($dueTime);
        $createdAt = Carbon::parse($createdAt);

        $difference = $dueTime->diffInHours($createdAt);


        if($difference <= 90){
            $time = $dueTime;
        }
        elseif ($difference <= 24) {
            $time = $createdAt->addMinutes(90);
        } elseif ($difference > 24 && $difference <= 72) {
            $time = $createdAt->addHours(16);
        } else {
            $time = $dueTime->subHours(48);
        }

        return $time->format('Y-m-d H:i:s');

    }

}

