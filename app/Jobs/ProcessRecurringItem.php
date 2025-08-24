<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use App\Models\ApiCall;

class ProcessRecurringItem implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $item,
        public float $value, 
        public string $moment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $address = ENV('MOCK_API_URL', 'https://very-slow-api.test');

        $payload = [
            'Item' => $this->item,
            'Value' => $this->value,
            'Moment' => $this->moment,
        ];

        $url = $address . '/orders';
        $startTime = microtime(true);
        $response = Http::withoutVerifying()->post($url, $payload);
        $duration = round(microtime(true) - $startTime, 2);

        ApiCall::create([
            'request_url' => $url,
            'response_body' => $response->body(),
            'status_code' => $response->status(),
            'response_time' => $duration,
        ])->save();
    }
}
