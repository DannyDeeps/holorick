<?php

require_once __DIR__ . '/inc/start.php';

use Discord\Discord;
use Discord\WebSockets\Event;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\User\Member;
use React\EventLoop\Loop;

use Holorick\Identify\Channels;
use Holorick\Cred\Cred;

$discord= new Discord([
    'token' => Cred::D_TOKEN,
    'loop' => Loop::get(),
    'disabledEvents' => [],
    'loadAllMembers' => true
]);

$discord->on('ready', function(Discord $discord)
{
    // Message Router
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord)
    {
        if ($message->author->user->bot) return;

        switch ($message->channel_id)
        {
            case Channels::MOE_SCREENSHOTS:
                validateScreenshotMessage($message);
            break;

            // Private DM
            case null:
                return;
            break;

            default:
                messageRouter($message, $discord);
            break;
        }
    });

    // Are you a Rick or a Morty?
    $discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord)
    {
        $question= new Embed($discord);
        $question
            ->setTitle('What up my Glip Glop!')
            ->setType(Embed::TYPE_RICH)
            ->setAuthor('Holo-Rick')
            ->setDescription("If you could solve all of life's mysteries, would you?")
            ->addField([
                'name' => 'Eh.. Whatever..',
                'value' => 1
            ])
            ->addField([
                'name' => 'Oh, that sounds like fun!',
                'value' => 2
            ]);

        $member->user->sendMessage("He.. Hey! This is a test to.. test.. if your testing... aw screw it, just answer the stupid question!", false, $question);
    });
});

$discord->run();

function validateScreenshotMessage(Message $message)
{
    if (empty($message->attachments[0])) {
        $message->delete();
        $message->reply('Hey.. th.. _blugggh_.. that doesn\'t go there!');
    }
}

function messageRouter(Message $message, Discord $discord)
{
    if (strstr($message->content, '!')) {
        switch ($message->content) {
            case '!testmsg':
                $question= new Embed($discord);
                $question
                    ->setTitle('What up my Glip Glop!')
                    ->setType(Embed::TYPE_RICH)
                    ->setAuthor('Holo-Rick')
                    ->setDescription("If you could solve all of life's mysteries, would you?")
                    ->addField([
                        'name' => 'Eh.. Whatever..',
                        'value' => 1
                    ])
                    ->addField([
                        'name' => 'Oh, that sounds like fun!',
                        'value' => 2
                    ]);

                $message->author->user->sendMessage("He.. Hey! This is a test to.. test.. if your testing... aw screw it, just answer the stupid question!", false, $question);
            break;
        }
    }
}