<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\ListMembers;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

/**
 * Update or create a list member in Mailchimp based on DB data
 */
class CreateOrUpdateListMember extends BaseListMemberJob
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
        // Get the list
        $list = (new MailchimpList)->findOrFail($this->id);

        // Get the member from the database
        $member = (new MailchimpListMember)->findOrFail($this->mid);

        // If we have a mailchimp id, update otherwise create a new list member
        if ($member->mailchimp_id) {
            $result = (new ListMembers)->update($list->mailchimp_id, $this->data['email_address'], $member->modelToMailchimp());
        } else {
            $result = (new ListMembers)->add($list->mailchimp_id, $member->modelToMailchimp());
        }

        // If it didn't work, fail
        if ($result->hasErrors()) {
            throw new Exception('Error modifying ' . $member->email_address . ' from mailchimp list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
        }

        // Since we got the member back from mailchimp as a freebie, may as well update the database with new subs/clicks etc
        $member->update($member->mailchimpToModel($result->getContents()));
    }
}
