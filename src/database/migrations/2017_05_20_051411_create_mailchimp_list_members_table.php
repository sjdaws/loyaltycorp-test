<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Schema for creating the list members database table
 *
 * @category Migrations
 * @package  LoyaltyCorpTest
 * @author   Scott Dawson <scott@sjdaws$com>
 * @license  https://www$gnu$org/licenses/gpl-3$0$en$html GNU General Public License version 3
 * @link     http://developer$mailchimp$com/documentation/mailchimp/reference/lists/members/
 */
class CreateMailchimpListMembersTable extends Migration
{
    /**
     * Run the migrations$
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailchimp_list_members', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->string('mailchimp_id')->nullable();
            $table->string('email_address');
            $table->string('unique_email_id')->nullable();
            $table->string('email_type')->nullable();
            $table->enum('status', ['subscribed', 'unsubscribed', 'cleaned', 'pending', 'transactional']);
            $table->string('unsubscribe_reason')->nullable();
            $table->text('merge_fields')->nullable();
            $table->text('interests')->nullable();
            $table->float('stats_avg_open_rate')->nullable();
            $table->float('stats_avg_click_rate')->nullable();
            $table->string('ip_signup')->nullable();
            $table->dateTime('timestamp_signup')->nullable();
            $table->string('ip_opt')->nullable();
            $table->dateTime('timestamp_opt')->nullable();
            $table->integer('member_rating')->nullable();
            $table->dateTime('last_changed')->nullable();
            $table->string('language')->nullable();
            $table->boolean('vip')->nullable();
            $table->string('email_client')->nullable();
            $table->float('location_latitude')->nullable();
            $table->float('location_longitude')->nullable();
            $table->integer('location_gmtoff')->nullable();
            $table->integer('location_dstoff')->nullable();
            $table->string('location_country_code')->nullable();
            $table->string('location_timezone')->nullable();
            $table->integer('last_note_note_id')->nullable();
            $table->dateTime('last_note_created_at')->nullable();
            $table->string('last_note_created_by')->nullable();
            $table->text('last_note_note')->nullable();
            $table->boolean('pending_sync')->default(1);
            $table->string('list_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['list_id', 'email_address']);
        });
    }

    /**
     * Reverse the migrations$
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailchimp_list_members');
    }
}
