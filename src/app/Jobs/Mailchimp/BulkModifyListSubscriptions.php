<?php

namespace Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp;

use Exception;
use Sjdaws\LoyaltyCorpMailchimp\Api\Lists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

/**
 * Bulk modify members within a list
 */
class BulkModifyListSubscriptions extends BaseListJob
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

        // Set up data we will send, we always want to update existing members
        $data = ['members' => $this->data, 'update_existing' => true];

        // Bulk update
        $result = (new Lists)->bulk($list->mailchimp_id, $data);

        // If it didn't work, fail
        if ($result->hasErrors()) {
            throw new Exception('Error sending bulk subscription data to mailchimp for list ' . $list->id . ': ' . $result->getErrorMessage());
        }

        // Process returned member records
        $contents = $result->getContents();

        $this->processMembers($list, $contents['new_members']);
        $this->processMembers($list, $contents['updated_members']);
    }

    /**
     * Process member response
     *
     * @param MailchimpList $list    The list to add the member to
     * @param array         $members The members array to process
     *
     * @return void
     *
     * @throws Exception If there is a database failure
     */
    public function processMembers(MailchimpList $list, array $members)
    {
        foreach ($members as $mcMember) {
            // Update based on email address since we may not have a mailchimp id for this user yet
            $member = (new MailchimpListMember)->where('list_id', $list->mailchimp_id)->where('email_address', $mcMember['email_address'])->first();

            if ($member) {
                $result = $member->update($member->mailchimpToModel($mcMember));
            } else {
                $member = new MailchimpListMember;
                $result = $member->create($member->mailchimpToModel($mcMember));
            }

            // If it didn't work, fail
            if (!$result) {
                throw new Exception('Error updating database for member ' . $mcMember['id'] . ': ' . $result->getErrorMessage());
            }
        }
    }
}
