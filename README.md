# Helpvars 1.5.1

A lightweight extension to make common segment and helper variables (such as segment_x_category_id) available globally.

Do you need to get the category ID of a specific URL segment? Helpvars has you covered. Do you have the same category short name in two different groups? Not a problem, Helpvars lets you specify which category group to get the ID from.

## Config Values

	$config['helpvars_set_category_vars'] = 'y'; // Set to 'n' to disable category logic and remove overhead

	$config['helpvars_max_empty_segments'] = 5; // Defaults to false, optionally set to integer representing number of empty segments to set category data for

## Global

{all_segments}

Returns all current URL segments separated by forward slash.

{base_segments}

Returns all current URL segments except pagination separated by forward slash.

{last_segment_primary}

Print out the last segment that is not a pagination segment. Useful if you don't know what number of segments you might have but need to get the last segment with or without pagination.

## Conditional Checks

{is_ajax}

Example:

	{if is_ajax}
		Your content is being loaded via Ajax. Fancy!
	{if:else}
		{redirect="404"}
	{/if}

{is_pjax}

Example:

	{if is_pjax}
		Your content is being requested via Pjax.
	{if:else}
		{redirect="404"}
	{/if}

{is_https}

Example:

	{if is_https}
		Your page is secure (provided your OS vendor has not fallen prey to strange goto fail bugs)!
	{/if}

{can_access_cp}

Example:

	{if can_access_cp == "y"}
		Howdy! You have control panel access. Be sure to do good and not evil with your powers!
	{/if}

	{if can_access_cp == "n"}
		Sorry, but we don't trust you with control panel access.
	{/if}

{logged_in_member_id}

Example:

	{if logged_in_member_id == "2"}
		Hey there, you must be a specific member!
	{/if}

{paginated}

Example:

	{if paginated}
		Your page is paginated. You've posted a lot of entries!
	{/if}

{not_paginated}

Example:

	{if not_paginated}
		Your page is not paginated, better publish a few more entries!
	{/if}

## Category Variables

Check a segment to see if it matches up with a category short name and return the ID. Optionally specify the group the category should be in so you don't run into conflicts. Keep in mind that category helpers exclude native pagination segments.

{segment_x_category_id}
{segment_x_category_name}
{segment_x_category_description}
{segment_x_category_image}
{segment_x_category_parent_id}
{segment_x_group_x_category_id}
{segment_x_group_x_category_name}
{segment_x_group_x_category_description}
{segment_x_group_x_category_image}
{segment_x_group_x_category_parent_id}
{last_segment_category_id}
{last_segment_category_name}
{last_segment_category_description}
{last_segment_category_image}
{last_segment_category_parent_id}
{last_segment_group_x_category_id}
{last_segment_group_x_category_name}
{last_segment_group_x_category_description}
{last_segment_group_x_category_image}
{last_segment_group_x_category_parent_id}
{segment_category_ids}
{segment_category_ids_any}
{segment_group_x_category_ids}
{segment_group_x_category_ids_any}

Example:

Let's say that you have a URL that looks like this:

	http://www.domain.com/space-blog/category/asteroids

Where "asteroids" is your category short name. So in your "section/category" template, you want to get the ID of that category from the third segment of the URL. So your Channel Entry tag would look something like this:

	{exp:channel:entries channel="space-blog" category="{segment_3_category_id}"}

Pretty painless and awesome.

But let's say that the category of asteroids is in Category Group 2 which is assigned to space-blog, but you also have a category of asteroids in Category Group 1 which is assigned to the channel "Space Exploration". The problem is the ID for Category Group 1 is being returned and so your Channel Entries tag is not returning any results. This is bad. That's why you can also optionally specify the Group ID so you always get the right results.

	{exp:channel:entries channel="spaced-blog" category="{segment_3_group_2_category_id"}

Now you will get the correct category ID.

## License

Copyright 2016 [Caddis Interactive, LLC](https://www.caddis.co). Licensed under the [Apache License, Version 2.0](https://github.com/caddis/helpvars/blob/master/LICENSE).