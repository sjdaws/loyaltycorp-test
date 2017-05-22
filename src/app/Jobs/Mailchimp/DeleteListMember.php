<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\ListMembers;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

/**
 * Delete a member from a list from Mailchimp
 */
class DeleteListMember extends BaseListMemberJob
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

        // Get the member from the database, it'll be soft deleted so ensure we check the trash
        $member = (new MailchimpListMember)->onlyTrashed()->where('id', $this->mid)->first();

        if (!$member) {
            throw new Exception('Unable to find member ' . $this->mid . ', maybe it has already been deleted');
        }

        // Perform the delete on Mailchimp
        $result = (new ListMembers)->delete($list->mailchimp_id, $member->email_address);

        // If it didn't work, fail
        if ($result->hasErrors()) {
            throw new Exception('Error deleting member ' . $member->email_address . ' from mailchimp list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
        }

        // Hard delete the record from the database
        $member->forceDelete();
    }
}
