# Helpvars 1.3.1

A lightweight extension to make common segment and helper variables (such as segment_x_category_id) available globally.

Do you need to get the category ID of a specific URL segment? Helpvars has you covered. Do you have the same category short name in two different groups? Not a problem, Helpvars lets you specify which category group to get the ID from.

## Global

{all_segments}

Returns all current URL segments separated by forward slash.

## Conditional Checks

{is_ajax}

Example:

	{if is_ajax}
		Your content is being loaded via Ajax. Fancy!
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

	{if can_access_cp}
		Howdy! You have Control Panel access. Be sure to do good and not evil with your powers!
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
{{last_segment_category_image}  
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

Copyright 2014 Caddis Interactive, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.