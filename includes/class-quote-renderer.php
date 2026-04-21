<?php

/**
 * Renderer for the flashblocks/flashblocks-quotes block.
 *
 * Exposes every markup fragment as a public property so theme code
 * can rearrange, replace, or extend any piece via the
 * `flashblocks_quote_renderer` filter.
 *
 * @package FlashblocksQuotes
 */

if (! defined('ABSPATH')) {
	exit;
}

class Flashblocks_Quote_Renderer {

	/** @var \WP_Block */
	public \WP_Block $block;

	/** @var int */
	public int $post_id = 0;

	/** @var \WP_Post|null */
	public ?\WP_Post $post = null;

	/** @var array */
	public array $attributes = [];

	/* ── Raw data ─────────────────────────────────────── */

	/** @var string Raw author name (unescaped). */
	public string $author_name = '';

	/** @var string Raw author role (unescaped). */
	public string $author_role = '';

	/** @var int Featured-image / photo attachment ID. */
	public int $photo_id = 0;

	/* ── Markup fragments ─────────────────────────────── */

	/** @var string Filtered post content (the quote body). */
	public string $quote_content = '';

	/** @var string <span class="author-name"> or empty. */
	public string $author_name_markup = '';

	/** @var string <span class="author-role"> or empty. */
	public string $author_role_markup = '';

	/** @var string <div class="photo-wrap"> or empty. */
	public string $photo_markup = '';

	/** @var string The <cite> block (wraps name + role by default). */
	public string $cite = '';

	/** @var string The full <figcaption> (wraps photo + cite by default). */
	public string $figcaption = '';

	/** @var string Block wrapper attributes string. */
	public string $wrapper_attrs = '';

	/* ── Constructor ──────────────────────────────────── */

	/**
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content (unused).
	 * @param \WP_Block $block     Block instance.
	 */
	public function __construct(array $attributes, string $content, \WP_Block $block) {
		$this->attributes = $attributes;
		$this->block      = $block;

		$this->resolve_post();

		if (! $this->post) {
			return;
		}

		$this->prepare_data();
		$this->build_markup();
	}

	/* ── Post resolution ─────────────────────────────── */

	private function resolve_post(): void {
		$context_is_query_loop = ! empty($this->block->context['postId'])
			&& ($this->block->context['postType'] ?? '') === 'flashblocks_quote';

		$this->post_id = ! empty($this->attributes['selectedPostId'])
			? (int) $this->attributes['selectedPostId']
			: ($context_is_query_loop ? (int) $this->block->context['postId'] : 0);

		if (! $this->post_id) {
			return;
		}

		$post = get_post($this->post_id);

		if (
			$post
			&& 'flashblocks_quote' === $post->post_type
			&& 'publish' === $post->post_status
		) {
			$this->post = $post;
		}
	}

	/* ── Data preparation ────────────────────────────── */

	private function prepare_data(): void {
		$this->quote_content = apply_filters('the_content', $this->post->post_content);

		$this->author_name = (string) get_post_meta($this->post_id, '_flashblocks_quote_author_name', true);
		$this->author_role = (string) get_post_meta($this->post_id, '_flashblocks_quote_author_role', true);
		$this->photo_id    = (int) get_post_thumbnail_id($this->post_id);
	}

	/* ── Markup building ─────────────────────────────── */

	private function build_markup(): void {
		// Wrapper attributes.
		$text_align    = $this->attributes['textAlign'] ?? '';
		$extra_classes = $text_align ? ['class' => 'has-text-align-' . esc_attr($text_align)] : [];
		$this->wrapper_attrs = get_block_wrapper_attributes($extra_classes);

		// Author name.
		if ($this->author_name) {
			$escaped = esc_html($this->author_name);
			$this->author_name_markup = "<span class=\"author-name\">{$escaped}</span>";
		}

		// Author role.
		if ($this->author_role) {
			$escaped = esc_html($this->author_role);
			$this->author_role_markup = "<span class=\"author-role\">{$escaped}</span>";
		}

		// Photo.
		if ($this->photo_id) {
			$img = wp_get_attachment_image(
				$this->photo_id,
				'medium',
				false,
				[
					'class' => 'author-photo',
					'alt'   => $this->author_name ? esc_attr($this->author_name) : '',
				]
			);
			if ($img) {
				$this->photo_markup = "<div class=\"photo-wrap\" aria-hidden=\"true\">{$img}</div>";
			}
		}

		// Cite (wraps name + role).
		if ($this->author_name_markup || $this->author_role_markup) {
			$this->cite = "<cite>{$this->author_name_markup}{$this->author_role_markup}</cite>";
		}

		// Figcaption (wraps photo + cite).
		if ($this->photo_markup || $this->cite) {
			$this->figcaption = "<figcaption>{$this->photo_markup}{$this->cite}</figcaption>";
		}
	}

	/* ── Render ───────────────────────────────────────── */

	/**
	 * Returns the final block HTML.
	 *
	 * Fires the `flashblocks_quote_renderer` filter before assembling
	 * the output, giving theme code a chance to rearrange any property.
	 *
	 * @return string
	 */
	public function render(): string {
		if (! $this->post) {
			return '';
		}

		/**
		 * Filters the quote renderer instance before output.
		 *
		 * Modify any public property to rearrange, replace, or extend
		 * the block markup.
		 *
		 * @param Flashblocks_Quote_Renderer $renderer The renderer instance.
		 */
		$renderer = apply_filters('flashblocks_quote_renderer', $this);

		return <<<HTM
<figure {$renderer->wrapper_attrs}>
	<blockquote>
		{$renderer->quote_content}
	</blockquote>
	{$renderer->figcaption}
</figure>
HTM;
	}
}
