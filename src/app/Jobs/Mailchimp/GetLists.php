<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\Lists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;

/**
 * Sync lists in Mailchimp with lists in the database, any information in Mailchimp will overwrite what is in the database.
 * Ideally we would fetch all lists everytime a list needs to be updated however there is overhead to this if there are
 * lists exceeding the maximum allowed to fetch at one time in Mailchimp. This job will iterate through the lists until
 * all lists are retrieved and update them in the database.
 */
class GetLists extends BaseListJob
{
    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws Exception If an api error was encountered
     */
    public function handle()
    {
        // Set defaults
        $offset = $lastResult = 0;

        // Set a high total which will force the loop to run at least once
        $total = PHP_INT_MAX;

        // Process until we've done all lists
        while ($offset < $total) {
            // Grab lists
            $lists = (new Lists)->getAll(['offset' => $offset, 'count' => 10]);

            // If we have an error, fail
            if ($lists->hasErrors()) {
                throw new Exception($lists->getErrorMessage());
            }

            // Get contents and update total count
            $contents = $lists->getContents();
            $total = $contents['total_items'];

            // Process lists
            $this->processLists($contents['lists']);

            // Update counters
            $lastResult = count($contents['lists']);
            $offset += $lastResult;
        }
    }

    /**
     * Process list response
     *
     * @param array $lists The lists array to process
     *
     * @return void
     *
     * @throws Exception If there is a database failure
     */
    public function processLists(array $lists = [])
    {
        // Process lists
        foreach ($lists as $mcList) {
            $list = (new MailchimpList)->where('mailchimp_id', $mcList['id'])->first();

            if ($list) {
                $result = $list->update($list->mailchimpToModel($mcList));
            } else {
                $list = new MailchimpList;
                $result = $list->create($list->mailchimpToModel($mcList));
            }

            // If it didn't work, fail
            if (!$result) {
                throw new Exception('Error updating database for list ' . $mcList['id'] . ': ' . $result->getErrorMessage());
            }
        }
    }
}
