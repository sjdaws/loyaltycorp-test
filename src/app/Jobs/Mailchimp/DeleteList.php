<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\Lists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;

/**
 * Delete a list from Mailchimp
 */
class DeleteList extends BaseListJob
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
        // Get the list from the database, it'll be soft deleted so ensure we check the trash
        $list = (new MailchimpList)->onlyTrashed()->where('id', $this->id)->first();

        if (!$list) {
            throw new Exception('Unable to find list ' . $this->id . ', maybe it has already been deleted');
        }

        // Perform the delete on Mailchimp
        $result = (new Lists)->delete($list->mailchimp_id);

        // If it didn't work, fail
        if ($result->hasErrors()) {
            throw new Exception('Error deleting list from mailchimp for list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
        }

        // Hard delete the record from the database
        $list->forceDelete();
    }
}
