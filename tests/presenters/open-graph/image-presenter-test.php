<?php

namespace Yoast\WP\SEO\Tests\Presenters\Open_Graph;

use Mockery;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter;
use Yoast\WP\SEO\Tests\TestCase;
use Brain\Monkey;

/**
 * Class Image_Presenter_Test
 *
 * @coversDefaultClass \Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter
 *
 * @group presenters
 * @group open-graph
 * @group open-graph-image
 */
class Image_Presenter_Test extends TestCase {

	/**
	 * @var Image_Presenter|Mockery\MockInterface
	 */
	protected $instance;

	/**
	 * @var Indexable_Presentation
	 */
	protected $presentation;

	/**
	 * Setup some mocks.
	 */
	public function setUp() {
		parent::setUp();

		$this->instance     = Mockery::mock( Image_Presenter::class )->makePartial();
		$this->presentation = new Indexable_Presentation();
	}

	/**
	 * Tests the present when no image is given.
	 *
	 * @covers ::present
	 */
	public function test_present_with_no_set_images() {
		$this->presentation->open_graph_images = [];

		$this->assertEmpty( $this->instance->present( $this->presentation ) );
	}

	/**
	 * Tests the presentation with a set image.
	 *
	 * @covers ::present
	 */
	public function test_present_with_a_set_image() {
		$image = [
			'url'    => 'https://example.com/image.jpg',
			'width'  => 100,
			'height' => 100,
		];

		$this->presentation->open_graph_images = [ $image ];

		$this->assertEquals(
			'<meta property="og:image" content="https://example.com/image.jpg" />' . PHP_EOL . "\t" . '<meta property="og:image:secure_url" content="https://example.com/image.jpg" />' . PHP_EOL . "\t" . '<meta property="og:image:width" content="100" />' . PHP_EOL . "\t" . '<meta property="og:image:height" content="100" />',
			$this->instance->present( $this->presentation )
		);
	}

	/**
	 * Tests the situation where the apply_filters returns a non-string value.
	 *
	 * @covers ::filter
	 */
	public function test_filter_wrong_image_url_returned() {
		Monkey\Functions\expect( 'apply_filters' )
			->once()
			->with( 'wpseo_opengraph_image', 'image.jpg', $this->presentation )
			->andReturn( false );

		$this->assertEquals( [ 'url' => 'image.jpg' ], $this->instance->filter( [ 'url' => 'image.jpg' ], $this->presentation ) );
	}

	/**
	 * Tests the situation where the apply_filters returns another image.
	 *
	 * @covers ::filter
	 */
	public function test_filter() {
		Monkey\Functions\expect( 'apply_filters' )
			->once()
			->with( 'wpseo_opengraph_image', 'image.jpg', $this->presentation )
			->andReturn( 'filtered_image.jpg' );

		$this->assertEquals( [ 'url' => 'filtered_image.jpg' ], $this->instance->filter( [ 'url' => 'image.jpg' ], $this->presentation ) );
	}


}
