<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\Lists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;

/**
 * Update or create a list in Mailchimp based on DB data
 */
class CreateOrUpdateList extends BaseListJob
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

        // If we have a mailchimp id, update otherwise create a new list
        if ($list->mailchimp_id) {
            $result = (new Lists)->update($list->mailchimp_id, $list->modelToMailchimp());
        } else {
            $result = (new Lists)->add($list->modelToMailchimp());
        }

        // If it didn't work, fail
        if ($result->hasErrors()) {
            throw new Exception('Error sending list to mailchimp for list ' . $list->id . ': ' . $result->getErrorMessage());
        }

        // Since we got the list back from mailchimp as a freebie, may as well update the database with new subs/clicks etc
        $list->update($list->mailchimpToModel($result->getContents()));
    }
}
