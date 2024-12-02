<?php
namespace App\Jobs;

use App\Events\AddScoreEvent;
use App\Helpers\MessageHelper;
use App\Models\User;
use App\Models\Score;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId, $score, $reason, $description;

    public function __construct($userId, $score, $reason, $description)
    {
        $this->userId = $userId;
        $this->score = $score;
        $this->reason = $reason;
        $this->description = $description;
    }

    public function handle()
    {
        $user = User::find($this->userId);

        Score::create([
            'user_id' => $user->id,
            'score' => $this->score,
            'reason' => $this->reason,
            'description' => $this->description,
        ]);

        $variables = [
            'user_name' => $user->name,
            'score_increase'=>$this->score,
        ];

        $message = MessageHelper::getMessage('add_score', $variables);


        broadcast(new AddScoreEvent($user,$message));
    }
}
