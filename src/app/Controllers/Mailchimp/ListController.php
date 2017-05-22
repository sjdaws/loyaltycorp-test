<?php

namespace Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\BulkModifyListSubscriptions;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\CreateOrUpdateList;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\DeleteList;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\GetList;
use Sjdaws\LoyaltyCorpTest\Jobs\Mailchimp\GetLists;
use Sjdaws\LoyaltyCorpTest\MailchimpList;
use Sjdaws\LoyaltyCorpTest\MailchimpListMember;

class ListController extends Controller
{
    /**
     * Validation ruleset
     *
     * @var array
     */
    private $rules = [
        'name' => 'required|max:100',
        'permission_reminder' => 'required',
        'notify_on_subscribe' => 'max:100',
        'notify_on_unsubscribe' => 'max:100',
        'contact_company' => 'required',
        'contact_address1' => 'required',
        'contact_city' => 'required',
        'contact_state' => 'required',
        'contact_zip' => 'required',
        'contact_country' => 'required|max:2',
        'campaign_defaults_from_name' => 'required|max:100',
        'campaign_defaults_from_email' => 'required|email|max:100',
        'campaign_defaults_subject' => 'required|max:150',
        'campaign_defaults_language' => 'required|max:2',
    ];

    /**
     * Update subscriptions within a list
     *
     * @param int $id The id of the list to update
     *
     * @return void
     */
    public function bulk($id, Request $request)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have already been deleted');
        }

        $invalidMembers = $data = [];

        // Grab email addresses and prune invalids
        if ($subscribes = $request->subscribe) {
            // Some people don't follow instructions well, if we don't have a comma explode by newline
            $delimiter = strpos($subscribes, ',') !== false ? ',' : PHP_EOL;

            // Explode and trim whitespace and remove empty strings
            $subscribes = explode($delimiter, $subscribes);
            $subscribes = array_filter(array_map('trim', $subscribes));

            // Validate each email and only keep valid email addresses
            foreach ($subscribes as $key => $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $invalidMembers[] = $email;
                    continue;
                }

                // Email must always be lower case
                $email = mb_strtolower($email);

                // Add to data
                $data[$email] = ['email_address' => $email, 'status' => 'subscribed'];
            }
        }

        // Sort out unsubscribes
        if ($statuses = $request->status) {
            if (is_array($statuses)) {
                foreach ($statuses as $email => $status) {
                    // If this email is in the subscribe list, ignore it, we will always subscribe them over anything else
                    if (array_key_exists($email, $data)) {
                        continue;
                    }

                    // Email should already be lower case but lower case it again
                    $data[mb_strtolower($email)] = ['email_address' => mb_strtolower($email), 'status' => $status];
                }
            }
        }

        // Get members which we need to update
        $dbMembers = (new MailchimpListMember)->where('list_id', $list->mailchimp_id)->whereIn('email_address', array_keys($data))->get();

        // Sort members by email address
        $members = [];
        foreach ($dbMembers as $member) {
            $members[$member->email_address] = $member;
        }

        // Get rid of db members to free up memory
        unset($dbMembers);

        // Update or create database records
        foreach ($data as $email => $datum) {
            if (isset($members[$email])) {
                // If status hasn't changed, remove it from data so we don't send MBs of information for a single change
                if ($members[$email]->status == $datum['status']) {
                    unset($data[$email]);
                    continue;
                }

                // Update record
                $members[$email]->update(['status' => $status]);
            } else {
                (new MailchimpListMember)->create(['list_id' => $list->mailchimp_id, 'email_address' => $email, 'status' => $datum['status']]);
            }
        }

        // Dispatch to job
        if (count($data)) {
            dispatch(new BulkModifyListSubscriptions($list->id, $data));
        } else {
            // If there was nothing to process, send a different success message
            if (count($invalidMembers)) {
                return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id])->with('warningMessage', 'No valid subscribes or unsubscribes were received to send to mailchimp, there were some invalid subscribes which were ignored:')->with('warningList', $invalidMembers);
            }

            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id])->with('successMessage', 'No valid subscribes or unsubscribes were received to send to mailchimp.');
        }

        // Return with success or warning depending on whether we have invalid members or not
        if (count($invalidMembers)) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id])->with('warningMessage', 'Bulk subscribes and unsubscribes processed successfully and will be populated to mailchimp shortly, there were some invalid subscribes which were ignored:')->with('warningList', $invalidMembers);
        }

        return (new Redirect)->getFacadeRoot()->route('mailchimp.member.index', ['id' => $list->id])->with('successMessage', 'Bulk subscribes and unsubscribes processed successfully and will be populated to mailchimp shortly.');
    }

    /**
     * Create a new list
     *
     * @return void
     */
    public function create()
    {
        return view('loyaltycorp-test::mailchimp.list.modify', ['list' => new MailchimpList]);
    }

    /**
     * Confirm deletion of a list
     *
     * @param int $id The id of the list to delete
     *
     * @return void
     */
    public function delete($id)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have already been deleted');
        }

        return view('loyaltycorp-test::mailchimp.list.delete', ['list' => $list]);
    }

    /**
     * Remove a list from Mailchimp
     *
     * @param int $id The id of the list to remove
     *
     * @return void
     */
    public function destroy($id)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have already been deleted');
        }

        // Sync changes with mailchimp
        dispatch(new DeleteList($list->id));

        // Soft delete list
        $list->delete();

        return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('successMessage', 'List successfuly deleted, it will be removed from Mailchimp shortly');
    }

    /**
     * Edit a list
     *
     * @return void
     */
    public function edit($id)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        return view('loyaltycorp-test::mailchimp.list.modify', ['list' => $list]);
    }

    /**
     * Display the dashboard which will show options to go to users or mailchimp management
     *
     * @return void
     */
    public function index()
    {
        // Really should paginate this but time constraints
        return view('loyaltycorp-test::mailchimp.list.index', ['lists' => (new MailchimpList)->all()]);
    }

    /**
     * Show a single list
     *
     * @param int $id The id of the list to show
     *
     * @return void
     */
    public function show($id)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        // If list doesn't have a mailchimp id, add info message
        if (!$list->mailchimp_id) {
            return view('loyaltycorp-test::mailchimp.list.show', ['list' => $list, 'infoMessage' => 'You are unable to perform any actions on this list until it has been synced with Mailchimp.']);
        }

        return view('loyaltycorp-test::mailchimp.list.show', ['list' => $list]);
    }

    /**
     * Create a new list
     *
     * @param Request $request The request object
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        if (!$list = (new MailchimpList)->create($request->all())) {
            return (new Redirect)->getFacadeRoot()->back()->withInput()->with('dangerMessage', 'Unable to save form, try again soon');
        }

        // Sync changes with mailchimp
        dispatch(new CreateOrUpdateList($list->id));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('successMessage', 'List successfuly created, it will populate to Mailchimp shortly');
    }

    /**
     * Add a job to the queue to sync a single list
     *
     * @param int $id The id of the list to sync
     *
     * @return void
     */
    public function sync($id)
    {
        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        dispatch(new GetList($list->id));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.list.show', ['id' => $list->id])->with('successMessage', 'A list sync request has been made, ideally this page would have some ajax running in the background to poll for the job success and refresh, but alas it doesn\'t');
    }

    /**
     * Add a job to the queue to sync all lists
     *
     * @return void
     */
    public function syncAll()
    {
        dispatch(new GetLists);
        return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('successMessage', 'A list sync request has been made, ideally this page would have some ajax running in the background to poll for the job success and refresh, but alas it doesn\'t');
    }

    /**
     * Update a list
     *
     * @param int $id The id of the list to update
     *
     * @return void
     */
    public function update($id, Request $request)
    {
        $this->validate($request, $this->rules);

        $list = (new MailchimpList)->find($id);

        if (!$list) {
            return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('dangerMessage', 'Unable to find list, it may have been deleted');
        }

        // Update list
        $list->update($request->all());

        // Sync changes with mailchimp
        dispatch(new CreateOrUpdateList($list->id));
        return (new Redirect)->getFacadeRoot()->route('mailchimp.list.index')->with('successMessage', 'List successfuly updated, the changes will populate to Mailchimp shortly');
    }
}
