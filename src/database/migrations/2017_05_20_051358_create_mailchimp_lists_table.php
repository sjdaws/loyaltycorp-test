<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Schema for creating the lists database table
 *
 * @category Migrations
 * @package  LoyaltyCorpTest
 * @author   Scott Dawson <scott@sjdaws$com>
 * @license  https://www$gnu$org/licenses/gpl-3$0$en$html GNU General Public License version 3
 * @link     http://developer$mailchimp$com/documentation/mailchimp/reference/lists/
 */
class CreateMailchimpListsTable extends Migration
{
    /**
     * Run the migrations$
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailchimp_lists', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';
            $table->increments('id');
            $table->string('mailchimp_id')->nullable()->unique();
            $table->integer('web_id')->nullable();
            $table->string('name');
            $table->string('contact_company');
            $table->string('contact_address1');
            $table->string('contact_address2')->nullable();
            $table->string('contact_city');
            $table->string('contact_state');
            $table->string('contact_zip');
            $table->string('contact_country');
            $table->string('contact_phone')->nullable();
            $table->text('permission_reminder');
            $table->boolean('use_archive_bar')->nullable();
            $table->string('campaign_defaults_from_name');
            $table->string('campaign_defaults_from_email');
            $table->string('campaign_defaults_subject')->nullable();
            $table->string('campaign_defaults_language');
            $table->string('notify_on_subscribe')->nullable();
            $table->string('notify_on_unsubscribe')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->integer('list_rating')->nullable();
            $table->boolean('email_type_option')->nullable();
            $table->string('subscribe_url_short')->nullable();
            $table->string('subscribe_url_long')->nullable();
            $table->string('beamer_address')->nullable();
            $table->enum('visibility', ['pub', 'prv'])->nullable();
            $table->text('modules')->nullable();
            $table->integer('stats_member_count')->nullable();
            $table->integer('stats_unsubscribe_count')->nullable();
            $table->integer('stats_cleaned_count')->nullable();
            $table->integer('stats_member_count_since_send')->nullable();
            $table->integer('stats_unsubscribe_count_since_send')->nullable();
            $table->integer('stats_cleaned_count_since_send')->nullable();
            $table->integer('stats_campaign_count')->nullable();
            $table->dateTime('stats_campaign_last_sent')->nullable();
            $table->integer('stats_merge_field_count')->nullable();
            $table->float('stats_avg_sub_rate')->nullable();
            $table->float('stats_avg_unsub_rate')->nullable();
            $table->float('stats_target_sub_rate')->nullable();
            $table->float('stats_open_rate')->nullable();
            $table->float('stats_click_rate')->nullable();
            $table->dateTime('stats_last_sub_date')->nullable();
            $table->dateTime('stats_last_unsub_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations$
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailchimp_lists');
    }
}
