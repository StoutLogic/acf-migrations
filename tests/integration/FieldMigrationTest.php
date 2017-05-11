<?php

use Codeception\TestCase\WPTestCase;
use StoutLogic\ACF\Migrations\FieldMigration;

class FieldMigrationTest  extends WPTestCase
{
    private $postId;
    private $termId;
    private $userId;
    private $commentId;

    public function setUp() {
        parent::setUp();

        $this->postId = $this->factory()->post->create([
            'post_type' => 'post',
            'meta_input' => [
                'image' => '30',
                '_image' => 'field_header_image_image',
            ]
        ]);

        $this->termId = $this->factory()->term->create([
            'taxonomy' => 'category',
        ]);
        add_term_meta($this->termId, 'image', '60');
        add_term_meta($this->termId, '_image', 'field_header_image_image');


        $this->userId = $this->factory()->user->create();
        add_user_meta($this->userId, 'image', '20');
        add_user_meta($this->userId, '_image', 'field_header_image_image');


        $this->commentId = $this->factory()->comment->create([
            'comment_post_ID' => $this->postId,
            'user_id' => $this->userId,
        ]);
        add_comment_meta($this->commentId, 'image', '10');
        add_comment_meta($this->commentId, '_image', 'field_header_image_image');



        add_option('header-defaults_image', '31');
        add_option('_header-defaults_image', 'field_header_image_image');

        add_option('footer-defaults_image', '61');
        add_option('_footer-defaults_image', 'field_header_image_image');

        add_option('option_image', '91');
        add_option('_option_image', 'field_header_image_image');
    }

    public function test_migrate_field_name()
    {
        $migrateField = new FieldMigration('field_header_image_image');
        $migrateField
            ->changeName('photo')
            ->migrate();

        $this->assertArraySubset([
            'photo' => ['30'],
            '_photo' => ['field_header_image_image'],
        ], get_post_meta($this->postId));

        $this->assertArraySubset([
            'photo' => ['60'],
            '_photo' => ['field_header_image_image'],
        ], get_term_meta($this->termId));

        $this->assertArraySubset([
            'photo' => ['20'],
            '_photo' => ['field_header_image_image'],
        ], get_user_meta($this->userId));

        $this->assertArraySubset([
            'photo' => ['10'],
            '_photo' => ['field_header_image_image'],
        ], get_comment_meta($this->commentId));
    }

    public function test_migrate_post_meta_key()
    {
        $migrateField = new FieldMigration('field_header_image_image');
        $migrateField
            ->changeKey('field_masthead_image_image')
            ->migrate();

        $this->assertArraySubset([
            'image' => ['30'],
            '_image' => ['field_masthead_image_image'],
        ], get_metadata('post', $this->postId));

        $this->assertArraySubset([
            'image' => ['60'],
            '_image' => ['field_masthead_image_image'],
        ], get_term_meta($this->termId));

        $this->assertArraySubset([
            'image' => ['20'],
            '_image' => ['field_masthead_image_image'],
        ], get_user_meta($this->userId));

        $this->assertArraySubset([
            'image' => ['10'],
            '_image' => ['field_masthead_image_image'],
        ], get_comment_meta($this->commentId));
    }

    public function test_migrate_field_name_and_then_key()
    {
        $migrateField = new FieldMigration('field_header_image_image');
        $migrateField
            ->changeName('photo')
            ->changeKey('field_masthead_image_photo')
            ->migrate();

        $this->assertArraySubset([
            'photo' => ['30'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_post_custom($this->postId));

        $this->assertArraySubset([
            'photo' => ['60'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_term_meta($this->termId));

        $this->assertArraySubset([
            'photo' => ['20'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_user_meta($this->userId));

        $this->assertArraySubset([
            'photo' => ['10'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_comment_meta($this->commentId));
    }

    public function test_migrate_field_key_and_then_name()
    {
        $migrateField = new FieldMigration('field_header_image_image');
        $migrateField
            ->changeKey('field_masthead_image_photo')
            ->changeName('photo')
            ->migrate();

        $this->assertArraySubset([
            'photo' => ['30'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_post_custom($this->postId));

        $this->assertArraySubset([
            'photo' => ['60'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_term_meta($this->termId));

        $this->assertArraySubset([
            'photo' => ['20'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_user_meta($this->userId));

        $this->assertArraySubset([
            'photo' => ['10'],
            '_photo' => ['field_masthead_image_photo'],
        ], get_comment_meta($this->commentId));
    }

    public function test_migation_field_key_of_options()
    {
        $migrateField = new FieldMigration('field_header_image_image');
        $migrateField
            ->includeOptionPage('header-defaults')
            ->excludeOptionPage('option')
            ->changeKey('field_header_image_photo')
            ->changeName('photo')
            ->migrate();

        $this->assertEquals(['options', 'header-defaults'], $migrateField->getOptionPages());

        $this->assertFalse(get_option('header-defaults_image'));
        $this->assertFalse(get_option('_header-defaults_image'));

        $this->assertEquals(31, get_option('header-defaults_photo'));
        $this->assertEquals('field_header_image_photo', get_option('_header-defaults_photo'));

        $this->assertFalse(get_option('footer-defaults_photo'));
        $this->assertFalse(get_option('_footer-defaults_photo'));
        $this->assertEquals(61, get_option('footer-defaults_image'));
        $this->assertEquals('field_header_image_image', get_option('_footer-defaults_image'));

        $this->assertFalse(get_option('option_photo'));
        $this->assertFalse(get_option('_option_photo'));
        $this->assertEquals(91, get_option('option_image'));
        $this->assertEquals('field_header_image_image', get_option('_option_image'));
    }
}
