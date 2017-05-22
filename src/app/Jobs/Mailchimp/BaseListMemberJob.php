<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * The base job for all list member processes to extend
 */
class BaseListMemberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The data to update on the list
     *
     * @var array
     */
    protected $data;

    /**
     * The id of the list
     *
     * @var int
     */
    protected $id;

    /**
     * The id of the member
     *
     * @var int
     */
    protected $mid;

    /**
     * If it fails more thsn 3 times, it ain't gonna work
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Capture the id
     *
     * @param int   $id   The id of the list being updated
     * @param int   $mid  The id of the member being updated
     * @param array $data Data to update the list with
     */
    public function __construct(int $id = 0, int $mid = 0, array $data = [])
    {
        $this->id = $id;
        $this->mid = $mid;
        $this->data = $data;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        error_log($exception);
    }
}
