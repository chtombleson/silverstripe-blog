<?php

namespace SilverStripe\Blog\Tests;

use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Dev\SapphireTest;

class BlogPostNotificationsTest extends SapphireTest
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected static $fixture_file = 'blog.yml';

    public function testUpdateNotificationRecipients()
    {
        if (!class_exists('CommentNotifier')) {
            $this->markTestSkipped('Comments Notification module is not installed');
        }

        $blogPost = $this->objFromFixture(BlogPost::class, 'PostC');
        $comment = new \SilverStripe\Comments\Model\Comment();
        $comment->Comment = 'This is a comment';
        $comment->write();
        $recipients = $blogPost->notificationRecipients(
            $comment
        )->toArray();

        $segments = [];
        foreach ($recipients as $recipient) {
            array_push($segments, $recipient->URLSegment);
        }

        sort($segments);
        $this->assertEquals(
            ['blog-contributor', 'blog-editor', 'blog-writer'],
            $segments
        );
    }

    public function testUpdateNotificationSubject()
    {
        if (!class_exists('CommentNotifier')) {
            $this->markTestSkipped('Comments Notification module is not installed');
        }
        $blogPost = $this->objFromFixture(BlogPost::class, 'PostC');
        $comment = new \SilverStripe\Comments\Model\Comment();
        $comment->Comment = 'This is a comment';
        $comment->write();
        $recipients = $blogPost->notificationRecipients(
            $comment
        )->toArray();
        $subject = $blogPost->notificationSubject($comment, $recipients[0]);
        $this->assertEquals(
            'A new comment has been posted on Third Post',
            $subject
        );
    }
}
