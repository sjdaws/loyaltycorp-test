<?php

namespace Sjdaws\LoyaltyCorpTest;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representation of the mailchimp_lists table
 */
class MailchimpList extends BaseModel
{
    /**
     * Use soft deletes for lists which are in a pending state of being deleted
     */
    use SoftDeletes;

    /**
     * Array keys for the member model
     *
     * @var array
     */
    protected $arrayKeys = ['contact', 'campaign_defaults'];

    /**
     * Allow this entire model to be filled from input
     *
     * @var bool
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
