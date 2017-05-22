<?php

namespace Sjdaws\LoyaltyCorpTest;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representation of the mailchimp_lists table
 */
class MailchimpListMember extends BaseModel
{
    /**
     * Use soft deletes for list removals awaiting sync
     */
    use SoftDeletes;

    /**
     * Array keys for the member model
     *
     * @var array
     */
    protected $arrayKeys = ['location'];

    /**
     * Allow this entire model to be filled from input
     *
     * @var bool
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Keys which shouldn't be expanded, but rather serialised
     *
     * @var array
     */
    protected $serialise = ['merge_fields', 'interests'];
}
