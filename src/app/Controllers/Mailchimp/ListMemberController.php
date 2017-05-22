<?php

namespace Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\CreateOrUpdateListMember;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\DeleteListMember;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\GetListMember;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\GetListMembers;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

class ListMemberController extends Controller
{
    /**
     * Validation ruleset
     *
     * @var array
     */
    private $rules = [
        'email_address' => 'required|email|max:255',
        'location_latitude' => 'numeric',
        'location_longitude' => 'numeric',
        'merge_keys.*' => 'max:255',
        'merge_values.*' => 'max:255',
    ];

    /**
     * Confirm removal of a member from a list
     *
     * @param int $id  The id of the list to delete the member from
     * @param int $mid The id of the member to remove
     *
     * @return void
     */
    public function delete($id, $mid)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have already been deleted');
        }

        // Get member
        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have been deleted');
        }

        return view('loyaltycorp-test::mailchimp.member.delete', ['list' => $list, 'member' => $member]);
    }

    /**
     * Remove the member from a list
     *
     * @param int $id  The id of the list to delete the member from
     * @param int $mid The id of the member to remove
     *
     * @return void
     */
    public function destroy($id, $mid)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have already been deleted');
        }

        // Get member
        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have already been deleted');
        }

        // Sync changes with mailchimp
        dispatch(new DeleteListMember($list->id, $member->id));

        // Soft delete member
        $member->delete();

        return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', $list->id)->with('successMessage', 'Member successfuly deleted, they will be removed from Mailchimp shortly');
    }

    /**
     * Modify a member
     *
     * @param int $id  The id of the list the member belongs to
     * @param int $mid The member id
     *
     * @return void
     */
    public function edit($id, $mid)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have been deleted');
        }

        return view('loyaltycorp-test::mailchimp.member.modify', ['list' => $list, 'member' => $member]);
    }

    /**
     * Display the members within a list
     *
     * @param int $id The id of the list to get members from
     *
     * @return void
     */
    public function index($id)
    {
        // Get list
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        // Get all members, this would normally be paginated
        $members = (new MailchimpListMember)->where('list_id', $list->mailchimp_id)->get();

        return view('loyaltycorp-test::mailchimp.member.index', ['list' => $list, 'members' => $members]);
    }

    /**
     * Show a single member
     *
     * @param int $id  The id of the list to show
     * @param int $mid The member to show
     *
     * @return void
     */
    public function show($id, $mid)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        // Get member
        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have been deleted');
        }

        // If member doesn't have a mailchimp id, add info message
        if (!$member->mailchimp_id) {
            return view('loyaltycorp-test::mailchimp.member.show', ['list' => $list, 'member' => $member, 'infoMessage' => 'You are unable to perform any actions on this member until they have been synced with Mailchimp.']);
        }

        return view('loyaltycorp-test::mailchimp.member.show', ['list' => $list, 'member' => $member]);
    }

    /**
     * Sync a single member
     *
     * @param int $id  The id of the list this member belongs to
     * @param int $mid The id of the member
     *
     * @return void
     */
    public function sync($id, $mid)
    {
        // Get list
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        // Get member
        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have been deleted');
        }

        dispatch(new GetListMember($list->id, $member->id));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.member.show', ['id' => $list->id, 'mid' => $member->id])->with('successMessage', 'A member sync request has been made, ideally this page would have some ajax running in the background to poll for the job success and refresh, but alas it doesn\'t');
    }

    /**
     * Sync members for a list
     *
     * @param int $id The id of the list to sync members for
     *
     * @return void
     */
    public function syncAll($id)
    {
        // Get list
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        dispatch(new GetListMembers($list->id));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id])->with('successMessage', 'A member sync request has been made, ideally this page would have some ajax running in the background to poll for the job success and refresh, but alas it doesn\'t');
    }

    /**
     * Update a member
     *
     * @param int $id          The id of the list the member belongs to
     * @param int $mid         The member id being updated
     * @param Request $request The request object
     *
     * @return void
     */
    public function update($id, $mid, Request $request)
    {
        // Update ruleset to ignore the current mid on the unique constraint
        $rules = $this->rules;
        $rules['email_address'] .= '|unique_with:mailchimp_list_members,list_id,' . $mid;

        $this->validate($request, $rules);

        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        $member = (new MailchimpListMember)->find($mid);

        if (!$member) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index')->with('dangerMessage', 'Unable to find member, they may have been deleted');
        }

        // Capture request object
        $data = $request->all();

        // Remove fake fields
        unset($data['merge_values']);

        // Add merge fields
        $data['merge_fields'] = [];
        foreach ($request->merge_values as $key => $value) {
            $data['merge_fields'][$key] = $value;
        }
        $data['merge_fields'] = serialize($data['merge_fields']);

        // Capture original email address in case it's changed so we don't lose the user
        $originalEmail = $member->email_address;

        // Update member
        $member->update($data);

        // Sync changes with mailchimp
        dispatch(new CreateOrUpdateListMember($list->id, $member->id, ['email_address' => $originalEmail]));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id, 'mid' => $member->id])->with('successMessage', 'Member successfuly updated, the changes will populate to Mailchimp shortly');
    }
}
