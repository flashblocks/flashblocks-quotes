<?php

/**
 * Dynamic render callback for the flashblocks/flashblocks-quotes block.
 *
 * Available variables:
 *   $attributes  – block attributes array
 *   $content     – inner block content (unused; dynamic block)
 *   $block       – WP_Block instance (provides $block->context)
 *
 * @package FlashblocksQuotes
 */

require_once dirname(__DIR__, 2) . '/includes/class-quote-renderer.php';

$renderer = new Flashblocks_Quote_Renderer($attributes, $content, $block);

echo $renderer->render();
