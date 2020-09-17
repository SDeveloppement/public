<?php

	add_filter('use_block_editor_for_post_type', '__return_false', 100);
	add_filter('gutenberg_can_edit_post_type', '__return_false');