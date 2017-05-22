<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\ListMembers;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

/**
 * Sync a single member from a single list.
 */
class GetListMember extends BaseListMemberJob
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
        // Get the list and member from the database
        $list = (new MailchimpList)->findOrFail($this->id);
        $member = (new MailchimpListMember)->findOrFail($this->mid);

        // Get mailchimp member details
        $mcMember = (new ListMembers)->get($list->mailchimp_id, $member->email_address);

        // If we have an error, fail
        if ($mcMember->hasErrors()) {
            throw new Exception($mcMember->getErrorMessage());
        }

        // Update member
        $result = $member->update($member->mailchimpToModel($mcMember->getContents()));

        // If it didn't work, fail
        if (!$result) {
            throw new Exception('Error updating database for member ' . $member->email_address . ' from mailchimp list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
        }
    }
}
