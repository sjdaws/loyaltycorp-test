<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\Lists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;

/**
 * Sync a single list with Mailchimp. The data in mailchimp will take precedence over the database data.
 */
class GetList extends BaseListJob
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
        // Get the list from the database
        $list = (new MailchimpList)->findOrFail($this->id);

        // Get mailchimp list
        $mcList = (new Lists)->get($list->mailchimp_id);

        // If we have an error, fail
        if ($mcList->hasErrors()) {
            throw new Exception($mcList->getErrorMessage());
        }

        // Find or create list
        $result = $list->update($list->mailchimpToModel($mcList->getContents()));

        // If it didn't work, fail
        if (!$result) {
            throw new Exception('Error updating database for list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
        }
    }
}
