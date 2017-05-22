<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\ListMembers;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

/**
 * Sync members from a single list, I know from experience this becomes almost impossible with large lists over 50k but
 * it should be fine for this test. Ideally with a larger list we'll need to keep track of what we've synced so we can
 * resume on failure
 */
class GetListMembers extends BaseListMemberJob
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

        // Get the list from the database
        $list = (new MailchimpList)->findOrFail($this->id);

        // Process until we've received all members
        while ($offset < $total) {
            // Grab members
            $members = (new ListMembers)->getAll($list->mailchimp_id, ['offset' => $offset, 'count' => 10]);

            // If we have an error, fail
            if ($members->hasErrors()) {
                throw new Exception($members->getErrorMessage());
            }

            // Get contents and update total count
            $contents = $members->getContents();
            $total = $contents['total_items'];

            // Process members
            $this->processMembers($list, $contents['members']);

            // Update counters
            $lastResult = count($contents['members']);
            $offset += $lastResult;
        }
    }

    /**
     * Process members response
     *
     * @param MailchimpList $list    The list to attach the members to
     * @param array         $members The members array to process
     *
     * @return void
     *
     * @throws Exception If there is a database failure
     */
    public function processMembers(MailchimpList $list, array $members = [])
    {
        // Process members
        foreach ($members as $mcMember) {
            // Find member by email since some won't have mailchimp ids
            $member = (new MailchimpListMember)->where('list_id', $list->mailchimp_id)->where('email_address', $mcMember['email_address'])->first();

            if ($member) {
                $result = $member->update($member->mailchimpToModel($mcMember));
            } else {
                $member = new MailchimpListMember;
                $result = $member->create($member->mailchimpToModel($mcMember));
            }

            // If it didn't work, fail
            if (!$result) {
                throw new Exception('Error updating database for member ' . $member->email_address . ' from mailchimp list ' . $list->mailchimp_id . ': ' . $result->getErrorMessage());
            }
        }
    }
}
